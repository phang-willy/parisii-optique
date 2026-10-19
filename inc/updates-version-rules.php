<?php
/**
 * Pure update rules. Safe to load without WordPress.
 */

/**
 * @param string $type WP_ENVIRONMENT_TYPE value.
 * @return string beta, stable, or an empty string when updates must stay silent.
 */
function parisii_updates_channel_for_environment($type) {
    $type = strtolower(trim((string) $type));
    if ($type === 'production') {
        return 'stable';
    }
    if ($type === 'staging') {
        return 'beta';
    }

    return '';
}

/**
 * @param string $channel beta or stable.
 * @param string $version Remote version.
 * @return bool
 */
function parisii_updates_is_channel_version($channel, $version) {
    if ($channel === 'stable') {
        return preg_match('/^\d+\.\d+\.\d+$/', $version) === 1;
    }
    if ($channel === 'beta') {
        return preg_match('/^\d+\.\d+\.\d+-beta\.\d+$/', $version) === 1;
    }

    return false;
}

/**
 * @param string $installed Installed theme version.
 * @param string $remote    Remote version.
 * @return bool
 */
function parisii_updates_is_newer($installed, $remote) {
    return version_compare($remote, $installed, '>');
}

/**
 * @param string $channel beta or stable.
 * @param string $slug    Theme or plugin directory name.
 * @param string $version Package version.
 * @return string
 */
function parisii_updates_package_url($channel, $slug, $version) {
    return 'https://updates.phangwilly.com/' . $channel . '/' . $slug . '-' . $version . '.zip';
}

/**
 * @param string $channel beta or stable.
 * @param string $slug    Theme or plugin directory name.
 * @param string $version Package version.
 * @param string $package Download URL.
 * @return bool
 */
function parisii_updates_package_is_allowed($channel, $slug, $version, $package) {
    return $package === parisii_updates_package_url($channel, $slug, $version);
}

/**
 * @param string               $channel   beta or stable.
 * @param string               $installed Installed version.
 * @param array<string,string> $item      Catalog item.
 * @return bool
 */
function parisii_updates_offer_is_installable($channel, $installed, $item) {
    $version = isset($item['version']) ? (string) $item['version'] : '';
    $package = isset($item['package']) ? (string) $item['package'] : '';
    $slug = isset($item['slug']) ? (string) $item['slug'] : '';

    return parisii_updates_is_channel_version($channel, $version)
        && parisii_updates_package_is_allowed($channel, $slug, $version, $package)
        && parisii_updates_is_newer($installed, $version);
}
