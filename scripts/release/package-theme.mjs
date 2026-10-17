import { spawnSync } from "node:child_process";
import { cpSync, mkdirSync, readdirSync, rmSync, statSync } from "node:fs";
import { tmpdir } from "node:os";
import { dirname, join, relative } from "node:path";
import { fileURLToPath } from "node:url";

const root = join(dirname(fileURLToPath(import.meta.url)), "..", "..");
const skipAnywhere = new Set([".git", "node_modules", ".github"]);
const skipTop = new Set([
    "tests",
    "src",
    "scripts",
    "updates-plugin",
    "package.json",
    "package-lock.json",
    "webpack.config.js",
    ".gitignore",
    ".cursorrules",
    "README.md",
    ".htaccess-upload-config",
    "release-artifacts",
]);

/**
 * @param {string} name
 * @param {boolean} top
 * @returns {boolean}
 */
function skipEntry(name, top) {
    if (skipAnywhere.has(name) || name.startsWith(".env") || name.endsWith("_ignore.md")) {
        return true;
    }
    return top && skipTop.has(name);
}

/**
 * @param {string} source
 * @param {string} destination
 * @param {boolean} top
 */
function copyTree(source, destination, top) {
    for (const entry of readdirSync(source, { withFileTypes: true })) {
        if (skipEntry(entry.name, top)) {
            continue;
        }
        const from = join(source, entry.name);
        const to = join(destination, entry.name);
        if (entry.isDirectory()) {
            mkdirSync(to, { recursive: true });
            copyTree(from, to, false);
            continue;
        }
        if (entry.isFile()) {
            cpSync(from, to);
        }
    }
}

/**
 * @param {string[]} args
 * @param {string} name
 * @returns {string}
 */
function flag(args, name) {
    const index = args.indexOf(name);
    if (index === -1 || !args[index + 1]) {
        throw new Error(`Option requise: ${name}`);
    }
    return args[index + 1];
}

/**
 * @param {string} version
 * @param {string} output
 */
export function packageTheme(version, output) {
    if (!/^\d+\.\d+\.\d+(?:-beta\.\d+)?$/.test(version)) {
        throw new Error(`Version refusee: ${version}`);
    }

    const css = statSync(join(root, "dist", "style.css"));
    if (css.size <= 10000) {
        throw new Error("dist/style.css est absent ou trop petit");
    }
    if (!statSync(join(root, "blocks", "dist", "blocks", "index.js")).isFile()) {
        throw new Error("blocks/dist/blocks/index.js est absent");
    }

    const stage = join(tmpdir(), `parisii-optique-release-${process.pid}`);
    const themeDir = join(stage, "parisii-optique");
    rmSync(stage, { recursive: true, force: true });
    mkdirSync(themeDir, { recursive: true });
    copyTree(root, themeDir, true);

    const style = join(themeDir, "style.css");
    const header = statSync(style);
    if (!header.isFile()) {
        throw new Error("style.css est absent de l'archive");
    }

    mkdirSync(dirname(output), { recursive: true });
    rmSync(output, { force: true });
    const zip = spawnSync("tar", ["-a", "-c", "-f", output, "-C", stage, "parisii-optique"], { stdio: "inherit" });
    if (zip.status !== 0) {
        throw new Error("Creation du ZIP impossible");
    }

    const listing = spawnSync("tar", ["-tf", output], { encoding: "utf8" });
    if (listing.status !== 0) {
        throw new Error("Lecture du ZIP impossible");
    }
    const entries = listing.stdout.split(/\r?\n/).filter(Boolean).map((entry) => entry.replace(/\\/g, "/"));
    const foreign = entries.filter((entry) => entry !== "parisii-optique" && !entry.startsWith("parisii-optique/"));
    if (foreign.length > 0) {
        throw new Error(`Dossier racine inattendu: ${foreign[0]}`);
    }
    for (const required of ["parisii-optique/style.css", "parisii-optique/functions.php", "parisii-optique/dist/style.css", "parisii-optique/blocks/dist/blocks/index.js"]) {
        if (!entries.includes(required)) {
            throw new Error(`Fichier manquant dans le ZIP: ${required}`);
        }
    }
    for (const entry of entries) {
        const path = relative("parisii-optique", entry).replace(/\\/g, "/");
        if (path === "node_modules" || path.startsWith("node_modules/") || path === ".git" || path.startsWith(".git/") || path.startsWith(".env")) {
            throw new Error(`Fichier interdit dans le ZIP: ${entry}`);
        }
    }

    rmSync(stage, { recursive: true, force: true });
    return output;
}

if (process.argv[1] && process.argv[1].endsWith("package-theme.mjs")) {
    try {
        const output = packageTheme(flag(process.argv.slice(2), "--version"), flag(process.argv.slice(2), "--output"));
        process.stdout.write(`${output}\n`);
    } catch (error) {
        process.stderr.write(`${error instanceof Error ? error.message : String(error)}\n`);
        process.exit(1);
    }
}
