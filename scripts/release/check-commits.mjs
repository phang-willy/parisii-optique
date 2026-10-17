import { execFileSync } from "node:child_process";

const base = process.env.BASE_SHA || "";
const head = process.env.HEAD_SHA || "";

if (base === "" || head === "") {
    process.stderr.write("BASE_SHA et HEAD_SHA sont requis\n");
    process.exit(1);
}

const output = execFileSync("git", ["log", "--format=%H%x09%s", `${base}..${head}`], { encoding: "utf8" }).trim();
if (output === "") {
    process.stdout.write("Aucun commit a verifier\n");
    process.exit(0);
}

const allowed = /^(feat|fix|docs|chore|refactor|test|ci|style|perf|build|revert)(\([^)\s]+\))?(!)?: \S/;
const failures = [];

for (const line of output.split(/\r?\n/)) {
    const [sha, subject] = line.split("\t");
    if (!subject || subject.startsWith("Merge ")) {
        continue;
    }
    if (!allowed.test(subject)) {
        failures.push(`${sha.slice(0, 7)} ${subject}`);
    }
}

if (failures.length > 0) {
    process.stderr.write(`Commits hors Conventional Commits:\n${failures.join("\n")}\n`);
    process.exit(1);
}

process.stdout.write("Conventional Commits OK\n");
