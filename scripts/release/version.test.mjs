import assert from "node:assert/strict";
import test from "node:test";
import {
    assertNewerMajor,
    bumpVersion,
    highestBump,
    nextBetaVersion,
    parseCommit,
    shouldCutBeta,
    renderPackageVersion,
    renderPluginHeader,
    renderStyleHeader,
} from "./version.mjs";

test("fix and feat choose the highest allowed bump", () => {
    assert.equal(parseCommit("fix: preserve titles"), "patch");
    assert.equal(parseCommit("feat: add components"), "minor");
    assert.equal(parseCommit("feat!: change contract"), "minor");
    assert.equal(parseCommit("chore: cleanup\n\nBREAKING CHANGE: drop hook"), "minor");
    assert.equal(parseCommit("docs: readme"), "none");
    assert.equal(parseCommit("chore(release): 1.2.0 [skip ci]"), "none");
    assert.equal(parseCommit("Merge pull request #80 from phang-willy/fix/seo"), "none");
    assert.equal(highestBump(["docs: note", "fix: a", "feat: b", "fix: c"]), "minor");
    assert.equal(highestBump(["chore: a", "refactor: b"]), "none");
});

test("stable bumps stay on the current major", () => {
    assert.equal(bumpVersion("1.2.3", "patch"), "1.2.4");
    assert.equal(bumpVersion("1.2.3", "minor"), "1.3.0");
    assert.equal(nextBetaVersion("1.3.0", ["v1.3.0-beta.1", "v1.3.0-beta.2", "v1.2.4-beta.4"]), "1.3.0-beta.3");
    assert.equal(nextBetaVersion("1.2.4", []), "1.2.4-beta.1");
    assert.equal(shouldCutBeta(0, []), true);
    assert.equal(shouldCutBeta(1, ["chore(release): 1.1.0-beta.1 [skip ci]"]), false);
    assert.equal(shouldCutBeta(1, ["fix: correct a price"]), true);
});

test("manual major must move forward and resets minor and patch", () => {
    assert.equal(assertNewerMajor("1.5.2", "2"), "2.0.0");
    assert.equal(assertNewerMajor("2.1.0", "3"), "3.0.0");
    assert.throws(() => assertNewerMajor("2.1.0", "2"), /pas superieure/);
    assert.throws(() => assertNewerMajor("2.1.0", "1"), /pas superieure/);
    assert.throws(() => assertNewerMajor("2.1.0", "deux"), /entier/);
});

test("style.css and package.json receive the same version", () => {
    const css = "/*\nTheme Name: Parisii Optique\nVersion: 1.0.0\nText Domain: parisii-optique\n*/\n";
    const rendered = renderStyleHeader(css, "1.3.0-beta.1");
    assert.match(rendered, /^Version: 1\.3\.0-beta\.1$/m);
    assert.match(rendered, /^Update URI: https:\/\/updates\.phangwilly\.com\/$/m);

    const replaced = renderStyleHeader(
        "/*\nVersion: 1.0.0\nUpdate URI: https://example.test/\n*/\n",
        "1.3.0",
    );
    assert.match(replaced, /^Update URI: https:\/\/updates\.phangwilly\.com\/$/m);
    assert.equal(JSON.parse(renderPackageVersion('{"name":"theme","version":"1.0.0"}\n', "1.3.0")).version, "1.3.0");

    const plugin = renderPluginHeader("<?php\n/**\n * Plugin Name: Parisii Optique\n * Version: 1.0.0\n */\ndefine('PARISII_OPTIQUE_PLUGIN_VERSION', '1.0.0');\n", "1.3.0-beta.1");
    assert.match(plugin, /^ \* Version: 1\.3\.0-beta\.1$/m);
    assert.match(plugin, /^ \* Update URI: https:\/\/updates\.phangwilly\.com\/$/m);
    assert.match(plugin, /define\('PARISII_OPTIQUE_PLUGIN_VERSION', '1\.3\.0-beta\.1'\);/);
});
