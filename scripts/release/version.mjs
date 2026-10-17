import { execFileSync } from "node:child_process";
import { readFileSync, writeFileSync } from "node:fs";
import { dirname, join } from "node:path";
import { fileURLToPath, pathToFileURL } from "node:url";

const root = join(dirname(fileURLToPath(import.meta.url)), "..", "..");

export const UPDATE_URI = "https://updates.phangwilly.com/";
export const THEME_SLUG = "parisii-optique";

/**
 * @param {string} message
 * @returns {"minor" | "patch" | "none"}
 */
export function parseCommit(message) {
    const text = String(message).replace(/\r\n/g, "\n").trim();
    if (text === "") {
        return "none";
    }

    const header = text.split("\n")[0].trim();
    if (header.startsWith("Merge ") || header.startsWith("chore(release):")) {
        return "none";
    }

    const match = header.match(/^([a-z]+)(?:\([^)\n]+\))?(!)?:\s+\S/);
    if (!match) {
        return "none";
    }

    const breaking = Boolean(match[2]) || /^BREAKING CHANGE:/m.test(text);
    if (breaking || match[1] === "feat") {
        return "minor";
    }
    if (match[1] === "fix") {
        return "patch";
    }

    return "none";
}

/**
 * @param {string[]} messages
 * @returns {"minor" | "patch" | "none"}
 */
export function highestBump(messages) {
    let level = "none";
    for (const message of messages) {
        const bump = parseCommit(message);
        if (bump === "minor") {
            return "minor";
        }
        if (bump === "patch") {
            level = "patch";
        }
    }
    return level;
}

/**
 * @param {string} tag
 * @returns {{major: number, minor: number, patch: number, version: string} | null}
 */
export function parseStableTag(tag) {
    const match = /^v(\d+)\.(\d+)\.(\d+)$/.exec(tag);
    if (!match) {
        return null;
    }

    return {
        major: Number(match[1]),
        minor: Number(match[2]),
        patch: Number(match[3]),
        version: `${match[1]}.${match[2]}.${match[3]}`,
    };
}

/**
 * @param {string} version
 * @param {"minor" | "patch"} level
 * @returns {string}
 */
export function bumpVersion(version, level) {
    const parsed = parseStableTag(`v${version}`);
    if (!parsed) {
        throw new Error(`Version stable invalide: ${version}`);
    }
    if (level === "patch") {
        return `${parsed.major}.${parsed.minor}.${parsed.patch + 1}`;
    }
    if (level === "minor") {
        return `${parsed.major}.${parsed.minor + 1}.0`;
    }
    throw new Error(`Niveau de version invalide: ${level}`);
}

/**
 * @param {string} version
 * @param {string[]} tags
 * @returns {string}
 */
export function nextBetaVersion(version, tags) {
    const pattern = new RegExp(`^v${version.replace(/\./g, "\\.")}-beta\\.(\\d+)$`);
    let max = 0;
    for (const tag of tags) {
        const match = pattern.exec(tag);
        if (match) {
            max = Math.max(max, Number(match[1]));
        }
    }
    return `${version}-beta.${max + 1}`;
}

/**
 * @param {string} currentVersion
 * @param {string} requested
 * @returns {string}
 */
export function assertNewerMajor(currentVersion, requested) {
    const current = parseStableTag(`v${currentVersion}`);
    if (!current) {
        throw new Error(`Version stable actuelle invalide: ${currentVersion}`);
    }
    if (!/^\d+$/.test(String(requested).trim())) {
        throw new Error("La version majeure doit etre un entier");
    }

    const major = Number(requested);
    if (major <= current.major) {
        throw new Error(`La majeure ${major} n'est pas superieure a ${current.major}`);
    }

    return `${major}.0.0`;
}

/**
 * @param {string} css
 * @param {string} version
 * @returns {string}
 */
export function renderStyleHeader(css, version) {
    if (!/^Version:\s*.+$/m.test(css)) {
        throw new Error("style.css ne contient pas le champ Version");
    }

    let next = css.replace(/^Version:\s*.+$/m, `Version: ${version}`);
    if (/^Update URI:/m.test(next)) {
        next = next.replace(/^Update URI:\s*.+$/m, `Update URI: ${UPDATE_URI}`);
    } else {
        next = next.replace(/^Version:\s*.+$/m, (line) => `${line}\nUpdate URI: ${UPDATE_URI}`);
    }

    return next;
}

/**
 * @param {string} jsonText
 * @param {string} version
 * @returns {string}
 */
export function renderPackageVersion(jsonText, version) {
    const data = JSON.parse(jsonText);
    data.version = version;
    return `${JSON.stringify(data, null, 2)}\n`;
}

/**
 * @param {string} php
 * @param {string} version
 * @returns {string}
 */
export function renderPluginHeader(php, version) {
    if (!/^ \* Version:\s*.+$/m.test(php)) {
        throw new Error("L'en-tete Version du plugin est absente");
    }

    let next = php.replace(/^ \* Version:\s*.+$/m, ` * Version: ${version}`);
    if (/^ \* Update URI:/m.test(next)) {
        next = next.replace(/^ \* Update URI:\s*.+$/m, ` * Update URI: ${UPDATE_URI}`);
    } else {
        next = next.replace(/^ \* Version:\s*.+$/m, (line) => `${line}\n * Update URI: ${UPDATE_URI}`);
    }
    if (next.includes("PARISII_OPTIQUE_PLUGIN_VERSION")) {
        next = next.replace(
            /define\('PARISII_OPTIQUE_PLUGIN_VERSION',\s*'[^']*'\);/,
            `define('PARISII_OPTIQUE_PLUGIN_VERSION', '${version}');`,
        );
    }

    return next;
}

/**
 * @param {string[]} args
 * @returns {string}
 */
function git(args) {
    return execFileSync("git", args, { cwd: root, encoding: "utf8" }).trim();
}

function listTags() {
    const output = git(["tag", "--list", "v*"]);
    return output === "" ? [] : output.split(/\r?\n/);
}

function latestStable(tags) {
    const stables = tags.map(parseStableTag).filter(Boolean);
    stables.sort((left, right) => left.major - right.major || left.minor - right.minor || left.patch - right.patch);
    return stables.at(-1) || null;
}

function commitsSince(tag) {
    const output = git(["log", `${tag}..HEAD`, "--format=%B%x1e"]);
    if (output === "") {
        return [];
    }
    return output.split("\x1e").map((entry) => entry.trim()).filter(Boolean);
}

/**
 * @param {string[]} args
 */
function flag(args, name) {
    const index = args.indexOf(name);
    if (index === -1) {
        return "";
    }
    return args[index + 1] || "";
}

function plan(channel, major) {
    const tags = listTags();
    const stable = latestStable(tags);
    if (!stable) {
        throw new Error("Aucun tag stable. Creez v1.0.0 sur origin/main avant une release.");
    }

    if (channel === "major") {
        const version = assertNewerMajor(stable.version, major);
        if (tags.includes(`v${version}`)) {
            throw new Error(`Le tag v${version} existe deja`);
        }
        return { release: true, version, tag: `v${version}`, channel: "stable" };
    }

    const bump = highestBump(commitsSince(`v${stable.version}`));
    if (bump === "none") {
        return {
            release: false,
            version: "",
            tag: "",
            channel,
            reason: `Aucun commit fix ou feat depuis v${stable.version}`,
        };
    }

    const next = bumpVersion(stable.version, bump);
    const version = channel === "beta" ? nextBetaVersion(next, tags) : next;
    if (tags.includes(`v${version}`)) {
        throw new Error(`Le tag v${version} existe deja`);
    }

    return { release: true, version, tag: `v${version}`, channel };
}

function apply(version) {
    if (!/^\d+\.\d+\.\d+(?:-beta\.\d+)?$/.test(version)) {
        throw new Error(`Version refusee: ${version}`);
    }

    const stylePath = join(root, "style.css");
    const packagePath = join(root, "package.json");
    writeFileSync(stylePath, renderStyleHeader(readFileSync(stylePath, "utf8"), version));
    writeFileSync(packagePath, renderPackageVersion(readFileSync(packagePath, "utf8"), version));
    for (const relativePath of [
        "parisii-optique-plugin/parisii-optique-plugin.php",
        "updates-plugin/parisii-updates.php",
    ]) {
        const pluginPath = join(root, relativePath);
        writeFileSync(pluginPath, renderPluginHeader(readFileSync(pluginPath, "utf8"), version));
    }
}

function main() {
    const args = process.argv.slice(2);
    const command = args[0];
    if (command === "plan") {
        const channel = flag(args, "--channel");
        if (!["beta", "stable", "major"].includes(channel)) {
            throw new Error("Canal attendu: beta, stable ou major");
        }
        process.stdout.write(`${JSON.stringify(plan(channel, flag(args, "--major")))}\n`);
        return;
    }
    if (command === "apply") {
        apply(flag(args, "--version"));
        return;
    }
    throw new Error("Commande attendue: plan ou apply");
}

if (process.argv[1] && pathToFileURL(process.argv[1]).href === import.meta.url) {
    try {
        main();
    } catch (error) {
        process.stderr.write(`${error instanceof Error ? error.message : String(error)}\n`);
        process.exit(1);
    }
}
