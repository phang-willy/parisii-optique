import { createHash } from "node:crypto";
import { readFileSync, writeFileSync } from "node:fs";
import { join } from "node:path";

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
 * @param {string} directory
 * @param {string} channel
 * @param {string} version
 * @returns {string}
 */
export function writeCatalog(directory, channel, version) {
    const base = `https://updates.phangwilly.com/${channel}`;
    const file = (slug) => `${slug}-${version}.zip`;
    const sha256 = (name) => createHash("sha256").update(readFileSync(join(directory, name))).digest("hex");
    const themeName = file("parisii-optique");
    const brandName = file("parisii-optique-plugin");
    const catalog = {
        channel,
        released_at: new Date().toISOString().replace(/\.\d{3}Z$/, "Z"),
        theme: {
            slug: "parisii-optique",
            version,
            package: `${base}/${themeName}`,
            sha256: sha256(themeName),
            requires: "6.8",
            requires_php: "8.3",
        },
        plugins: [
            {
                slug: "parisii-optique-plugin",
                file: "parisii-optique-plugin/parisii-optique-plugin.php",
                version,
                package: `${base}/${brandName}`,
                sha256: sha256(brandName),
            },
        ],
    };
    const output = join(directory, "latest.json");
    writeFileSync(output, `${JSON.stringify(catalog, null, 2)}\n`);
    return output;
}

if (process.argv[1] && process.argv[1].endsWith("write-catalog.mjs")) {
    try {
        const output = writeCatalog(
            flag(process.argv.slice(2), "--dir"),
            flag(process.argv.slice(2), "--channel"),
            flag(process.argv.slice(2), "--version"),
        );
        process.stdout.write(`${output}\n`);
    } catch (error) {
        process.stderr.write(`${error instanceof Error ? error.message : String(error)}\n`);
        process.exit(1);
    }
}
