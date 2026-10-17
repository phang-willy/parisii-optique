#!/usr/bin/env bash
set -euo pipefail

: "${SSH_HOST:?SSH_HOST manquant}"
: "${SSH_PORT:?SSH_PORT manquant}"
: "${SSH_USER:?SSH_USER manquant}"
: "${SSH_KEY:?SSH_KEY manquant}"
: "${SSH_PATH:?SSH_PATH manquant}"
: "${CHANNEL:?CHANNEL manquant}"
: "${VERSION:?VERSION manquant}"
: "${CATALOG:?CATALOG manquant}"
: "${ARTIFACTS_DIR:?ARTIFACTS_DIR manquant}"

if [[ "$CHANNEL" != "beta" && "$CHANNEL" != "stable" ]]; then
  echo "Canal invalide: $CHANNEL" >&2
  exit 1
fi

if [[ "$CHANNEL" == "stable" && ! "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
  echo "La production refuse la version $VERSION" >&2
  exit 1
fi

if [[ "$CHANNEL" == "beta" && ! "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+-beta\.[0-9]+$ ]]; then
  echo "La preproduction refuse la version $VERSION" >&2
  exit 1
fi

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
FINGERPRINT="$(tr -d '[:space:]' < "$ROOT/scripts/release/ssh-ed25519.fingerprint")"
KEY_FILE="$(mktemp)"
KNOWN="$(mktemp)"
cleanup() {
  rm -f "$KEY_FILE" "$KNOWN"
}
trap cleanup EXIT
printf '%s\n' "$SSH_KEY" > "$KEY_FILE"
chmod 600 "$KEY_FILE"

ssh-keyscan -p "$SSH_PORT" -T 15 -t ed25519 "$SSH_HOST" > "$KNOWN"
if ! ssh-keygen -lf "$KNOWN" | grep -F -q "$FINGERPRINT"; then
  echo "Empreinte SSH inattendue pour $SSH_HOST" >&2
  ssh-keygen -lf "$KNOWN" >&2 || true
  exit 1
fi

SSH_BASE=(-i "$KEY_FILE" -o IdentitiesOnly=yes -o StrictHostKeyChecking=yes -o UserKnownHostsFile="$KNOWN")

remote() {
  ssh "${SSH_BASE[@]}" -p "$SSH_PORT" "$SSH_USER@$SSH_HOST" "$@"
}

quote() {
  printf '%q' "$1"
}

PATH_Q="$(quote "$SSH_PATH")"
if ! remote "test -d $PATH_Q"; then
  echo "Le chemin SSH n'existe pas: $SSH_PATH" >&2
  exit 1
fi

BASE_NAME="$(remote "basename -- $PATH_Q")"
if remote "test -d $PATH_Q/wordpress-updates"; then
  DEST="$SSH_PATH/wordpress-updates"
elif [[ "$BASE_NAME" == "wordpress-updates" ]]; then
  DEST="$SSH_PATH"
else
  echo "wordpress-updates est introuvable dans $SSH_PATH" >&2
  exit 1
fi

PACKAGE_LIST="$(mktemp)"
CHANNEL="$CHANNEL" VERSION="$VERSION" CATALOG="$CATALOG" node --input-type=module > "$PACKAGE_LIST" <<'EOF'
import { readFileSync } from "node:fs";
const catalog = JSON.parse(readFileSync(process.env.CATALOG, "utf8"));
if (catalog.channel !== process.env.CHANNEL) {
  process.stderr.write("Le catalogue ne correspond pas au canal\n");
  process.exit(1);
}
const items = [catalog.theme, ...(catalog.plugins || [])];
for (const item of items) {
  if (item.version !== process.env.VERSION) {
    process.stderr.write(`Version inattendue pour ${item.slug}\n`);
    process.exit(1);
  }
  const name = item.package.split("/").pop();
  process.stdout.write(`${name}\t${item.sha256}\t${item.package}\n`);
}
EOF
mapfile -t PACKAGES < "$PACKAGE_LIST"
rm -f "$PACKAGE_LIST"
if [[ ${#PACKAGES[@]} -eq 0 ]]; then
  echo "Aucune archive a publier" >&2
  exit 1
fi

for package in "${PACKAGES[@]}"; do
  IFS=$'\t' read -r FILE_NAME LOCAL_SHA URL <<< "$package"
  DEST_FILE="$DEST/$FILE_NAME"
  scp "${SSH_BASE[@]}" -P "$SSH_PORT" "$ARTIFACTS_DIR/$FILE_NAME" "$SSH_USER@$SSH_HOST:$DEST_FILE"
  remote "chmod 644 $(quote "$DEST_FILE")"
  REMOTE_SHA="$(remote "sha256sum $(quote "$DEST_FILE") | awk '{print \$1}'")"
  if [[ "$LOCAL_SHA" != "$REMOTE_SHA" ]]; then
    echo "SHA-256 distant different pour $FILE_NAME" >&2
    exit 1
  fi
  HTTP_SHA="$(curl -fsSL "$URL" | sha256sum | awk '{print $1}')"
  if [[ "$HTTP_SHA" != "$LOCAL_SHA" ]]; then
    echo "Le ZIP public ne correspond pas pour $FILE_NAME" >&2
    exit 1
  fi
  echo "ZIP disponible: $URL"
done

TMP_NAME="latest.json.tmp.${RANDOM}"
remote "cat > $(quote "$DEST/$TMP_NAME") && mv -f $(quote "$DEST/$TMP_NAME") $(quote "$DEST/latest.json") && chmod 644 $(quote "$DEST/latest.json")" < "$CATALOG"

META="$(curl -fsSL "https://updates.phangwilly.com/${CHANNEL}/latest.json")"
printf '%s\n' "$META" | grep -F -q "\"version\": \"${VERSION}\""
echo "Catalogue publie sur https://updates.phangwilly.com/${CHANNEL}/latest.json"
