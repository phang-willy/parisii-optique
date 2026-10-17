import { spawnSync } from "node:child_process";
import { cpSync, mkdirSync, rmSync } from "node:fs";
import { tmpdir } from "node:os";
import { dirname, join } from "node:path";
import { fileURLToPath } from "node:url";

const root = join(dirname(fileURLToPath(import.meta.url)), "..", "..");

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
 * @param {string} slug
 * @param {string} sourceRelative
 * @param {string} mainFile
 * @param {string} version
 * @param {string} output
 * @returns {string}
 */
export function packagePlugin(slug, sourceRelative, mainFile, version, output) {
    if (!/^[a-z0-9-]+$/.test(slug)) {
        throw new Error(`Slug refuse: ${slug}`);
    }
    if (!/^\d+\.\d+\.\d+(?:-beta\.\d+)?$/.test(version)) {
        throw new Error(`Version refusee: ${version}`);
    }

    const stage = join(tmpdir(), `parisii-plugin-${slug}-${process.pid}`);
    const pluginDir = join(stage, slug);
    rmSync(stage, { recursive: true, force: true });
    mkdirSync(pluginDir, { recursive: true });
    cpSync(join(root, sourceRelative), pluginDir, { recursive: true });

    mkdirSync(dirname(output), { recursive: true });
    rmSync(output, { force: true });
    const zip = spawnSync("tar", ["-a", "-c", "-f", output, "-C", stage, slug], { stdio: "inherit" });
    if (zip.status !== 0) {
        throw new Error(`Creation du ZIP impossible pour ${slug}`);
    }

    const listing = spawnSync("tar", ["-tf", output], { encoding: "utf8" });
    if (listing.status !== 0) {
        throw new Error(`Lecture du ZIP impossible pour ${slug}`);
    }
    const entries = listing.stdout.split(/\r?\n/).filter(Boolean).map((entry) => entry.replace(/\\/g, "/"));
    const required = `${slug}/${mainFile}`;
    if (!entries.includes(required)) {
        throw new Error(`Fichier manquant dans le ZIP: ${required}`);
    }
    const foreign = entries.filter((entry) => entry !== slug && !entry.startsWith(`${slug}/`));
    if (foreign.length > 0) {
        throw new Error(`Dossier racine inattendu: ${foreign[0]}`);
    }

    rmSync(stage, { recursive: true, force: true });
    return output;
}

if (process.argv[1] && process.argv[1].endsWith("package-plugin.mjs")) {
    try {
        const output = packagePlugin(
            flag(process.argv.slice(2), "--slug"),
            flag(process.argv.slice(2), "--source"),
            flag(process.argv.slice(2), "--main"),
            flag(process.argv.slice(2), "--version"),
            flag(process.argv.slice(2), "--output"),
        );
        process.stdout.write(`${output}\n`);
    } catch (error) {
        process.stderr.write(`${error instanceof Error ? error.message : String(error)}\n`);
        process.exit(1);
    }
}
