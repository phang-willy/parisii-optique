<?php
/**
 * CLI checks for the theme helpers that do not need WordPress.
 *
 * Run: php tests/run.php
 */

if (PHP_SAPI !== 'cli') {
    exit;
}

require dirname(__DIR__) . '/inc/helpers-pure.php';
require dirname(__DIR__) . '/inc/updates-version-rules.php';

$failures = 0;

/**
 * @param bool   $condition Condition.
 * @param string $message   Failure message.
 */
function parisii_assert($condition, $message) {
    global $failures;
    if ($condition) {
        echo "OK  {$message}\n";
        return;
    }

    $failures++;
    echo "FAIL {$message}\n";
}

$legacy = parisii_optique_resolve_opening_hours([
    'hours_weekdays' => '9h30 - 19h00',
    'hours_saturday' => '10h00 - 19h30',
    'hours_sunday'   => 'Fermé',
]);
parisii_assert($legacy['monday']['opens'] === '14:00' && $legacy['monday']['closes'] === '19:00', 'legacy default splits Monday to 14:00-19:00');
parisii_assert($legacy['tuesday']['opens'] === '09:30' && $legacy['friday']['closes'] === '19:00', 'legacy default keeps Tuesday-Friday at 09:30-19:00');
parisii_assert($legacy['saturday']['opens'] === '10:00' && $legacy['saturday']['closes'] === '19:30', 'stored Saturday is preserved');
parisii_assert($legacy['sunday']['closed'] === true && $legacy['sunday']['schema'] === false, 'Sunday stays closed and out of schema times');

$custom = parisii_optique_resolve_opening_hours([
    'hours_weekdays' => '10h00 - 18h00',
]);
parisii_assert($custom['monday']['label'] === '10h00 - 18h00' && $custom['tuesday']['label'] === '10h00 - 18h00', 'custom legacy weekdays are not overwritten');

$saved = parisii_optique_resolve_opening_hours([
    'hours_weekdays'        => '10h00 - 18h00',
    'hours_monday'          => '14h00 - 19h00',
    'hours_tuesday_friday'  => '09h30 - 19h00',
]);
parisii_assert($saved['monday']['opens'] === '14:00' && $saved['tuesday']['opens'] === '09:30', 'saved split hours win over the legacy field');

$rows = parisii_optique_opening_hours_rows($legacy);
parisii_assert($rows[0]['label'] === 'Lundi' && $rows[1]['label'] === 'Mardi - vendredi', 'different weekday hours stay on two rows');
$grouped = parisii_optique_opening_hours_rows($custom);
parisii_assert($grouped[0]['label'] === 'Lundi - vendredi', 'identical weekday hours stay on one row');

$schema = parisii_optique_opening_hours_schema($legacy);
$days = [];
foreach ($schema as $specification) {
    foreach ($specification['dayOfWeek'] as $day) {
        $days[] = $day;
    }
}
parisii_assert(!in_array('Sunday', $days, true) && in_array('Monday', $days, true) && in_array('Friday', $days, true), 'schema lists open days and omits Sunday');

$closed = parisii_optique_parse_opening_hours('Fermé');
parisii_assert($closed['closed'] === true && $closed['schema'] === false && $closed['opens'] === '' && $closed['closes'] === '', 'Fermé has no opening range');

$hour_cases = [
    '9h - 19h'       => ['09:00', '19:00'],
    '9h30 - 19h'     => ['09:30', '19:00'],
    '9h - 19h30'     => ['09:00', '19:30'],
    '09h30 - 19h00'  => ['09:30', '19:00'],
];
foreach ($hour_cases as $input => $expected) {
    $warning = null;
    set_error_handler(static function ($severity, $message) use (&$warning) {
        $warning = $message;
        return true;
    });
    $parsed = parisii_optique_parse_opening_hours($input);
    restore_error_handler();
    parisii_assert(
        $warning === null && $parsed['opens'] === $expected[0] && $parsed['closes'] === $expected[1] && $parsed['schema'] === true,
        $input . ' parses to ' . $expected[0] . '-' . $expected[1] . ' without a warning'
    );
}

$invalid_hours = parisii_optique_parse_opening_hours('25h - 19h');
parisii_assert($invalid_hours['schema'] === false && $invalid_hours['opens'] === '' && $invalid_hours['closes'] === '', 'invalid hours do not invent a range');

$author_title = parisii_optique_finalize_document_title_parts(
    ['title' => 'Ada Lovelace', 'site' => 'Parisii Optique', 'tagline' => 'Opticien'],
    '',
    'Parisii Optique',
    false
);
parisii_assert(
    $author_title['title'] === 'Ada Lovelace' && ($author_title['site'] ?? '') === 'Parisii Optique' && !isset($author_title['tagline']),
    'author archive keeps the native title'
);

$date_title = parisii_optique_finalize_document_title_parts(
    ['title' => 'octobre 2026'],
    'octobre 2026',
    'Parisii Optique',
    false
);
parisii_assert(
    $date_title['title'] === 'octobre 2026' && ($date_title['site'] ?? '') === 'Parisii Optique',
    'date archive title is not replaced by the site name'
);

$term_title = parisii_optique_finalize_document_title_parts(
    ['title' => 'Archives'],
    'Lunettes',
    'Parisii Optique',
    false
);
parisii_assert($term_title['title'] === 'Lunettes' && ($term_title['site'] ?? '') === 'Parisii Optique', 'taxonomy archive keeps its explicit title');

$duplicate_title = parisii_optique_finalize_document_title_parts(
    ['title' => 'Parisii Optique'],
    'Parisii Optique',
    'Parisii Optique',
    false
);
parisii_assert($duplicate_title['title'] === 'Parisii Optique' && !isset($duplicate_title['site']), 'site name is not repeated on an archive title');

$front_title = parisii_optique_finalize_document_title_parts(
    ['title' => 'Parisii Optique', 'site' => 'Parisii Optique'],
    'Opticien à Cormeilles-en-Parisis — Parisii Optique',
    'Parisii Optique',
    true
);
parisii_assert(
    $front_title['title'] === 'Opticien à Cormeilles-en-Parisis — Parisii Optique' && !isset($front_title['site']),
    'front page keeps the editorial title without a second site name'
);

$city = parisii_optique_split_city_line('95240 Cormeilles-en-Parisis');
parisii_assert($city['postal_code'] === '95240' && $city['locality'] === 'Cormeilles-en-Parisis', 'postal code and city are split');

$embed = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2619.8573265004266!2d2.1749084767768863!3d48.95620249381781!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e661846293ff35%3A0x66ed9ca11dace44f!2sParisii%20Optique!5e0!3m2!1sfr!2sfr" style="border:0;height:100%" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>';
$iframe = parisii_optique_prepare_maps_iframe_html($embed);
parisii_assert(str_contains($iframe, 'title="Carte Parisii Optique"'), 'map iframe receives an accessible title');
parisii_assert(str_contains($iframe, 'loading="lazy"'), 'map iframe keeps lazy loading');
parisii_assert(str_contains($iframe, 'width:100%'), 'map iframe fills its container');
parisii_assert(str_contains($iframe, 'width="600"') && str_contains($iframe, 'height="450"'), 'lazy map iframe has explicit dimensions');
parisii_assert(parisii_optique_prepare_maps_iframe_html('<iframe src="https://evil.example/maps"></iframe>') === '', 'non-Google iframe is rejected');
parisii_assert(parisii_optique_prepare_maps_iframe_html('<iframe src="javascript:alert(1)"></iframe>') === '', 'javascript iframe src is rejected');

$coordinates = parisii_optique_maps_embed_coordinates(parisii_optique_maps_iframe_src($embed));
parisii_assert(is_array($coordinates) && abs($coordinates['latitude'] - 48.95620249381781) < 0.000001, 'coordinates come from the embed URL');
parisii_assert(parisii_optique_maps_embed_coordinates('https://www.google.com/maps/embed') === null, 'embed without coordinates does not invent geo data');

$link = parisii_optique_maps_external_url('12 Véloroute Sequana', '95240 Cormeilles-en-Parisis');
parisii_assert(str_starts_with($link, 'https://www.google.com/maps/search/?api=1&query='), 'external map link is a search URL');

$canonical = parisii_optique_rewrite_origin('http://po.phangwilly.com/collections/?x=1', 'https://parisii-optique.fr');
parisii_assert($canonical === 'https://parisii-optique.fr/collections/?x=1', 'production canonical keeps the path and uses the official origin');

$uniform = parisii_optique_choose_placeholder_relative([
    'woocommerce-placeholder.webp',
    '2026/02/woocommerce-placeholder-1024x1024-1.webp',
]);
parisii_assert($uniform === 'woocommerce-placeholder.webp', 'uniform placeholder wins when the root file exists');

$legacy = parisii_optique_choose_placeholder_relative([
    '2026/02/woocommerce-placeholder-1024x1024-1.webp',
]);
parisii_assert($legacy === '2026/02/woocommerce-placeholder-1024x1024-1.webp', 'dated production file is used only while the root file is absent');
parisii_assert(parisii_optique_choose_placeholder_relative([]) === '', 'missing custom files do not invent a path');

$local_url = parisii_optique_join_upload_url('http://localhost/parisii-optique/wp-content/uploads', 'woocommerce-placeholder.webp');
parisii_assert($local_url === 'http://localhost/parisii-optique/wp-content/uploads/woocommerce-placeholder.webp', 'placeholder URL uses the current uploads base');
parisii_assert(!str_contains(parisii_optique_join_upload_url('https://example.test/wp-content/uploads', 'woocommerce-placeholder.webp'), 'parisii-optique.fr'), 'placeholder host is not hardcoded');

$srcset = parisii_optique_build_placeholder_srcset('https://example.test/wp-content/uploads', [
    'woocommerce-placeholder.webp',
    'woocommerce-placeholder-300x300.webp',
], 1200);
parisii_assert(str_contains($srcset, 'woocommerce-placeholder-300x300.webp 300w'), 'srcset keeps an existing sized file');
parisii_assert(!str_contains($srcset, 'woocommerce-placeholder-1024x1024.webp'), 'srcset omits a sized file that is not on disk');
parisii_assert(parisii_optique_build_placeholder_srcset('https://example.test/wp-content/uploads', [
    '2026/02/woocommerce-placeholder-1024x1024-1.webp',
], 1024) === '', 'dated fallback does not advertise missing root sizes');

parisii_assert(parisii_updates_channel_for_environment('production') === 'stable', 'production reads the stable channel');
parisii_assert(parisii_updates_channel_for_environment('staging') === 'beta', 'staging reads the beta channel');
parisii_assert(parisii_updates_channel_for_environment('local') === '', 'local does not request updates');
parisii_assert(parisii_updates_channel_for_environment('') === '', 'a missing environment does not request updates');
parisii_assert(parisii_updates_is_channel_version('stable', '1.3.0') === true, 'stable accepts 1.3.0');
parisii_assert(parisii_updates_is_channel_version('stable', '1.3.0-beta.1') === false, 'stable rejects a beta version');
parisii_assert(parisii_updates_is_channel_version('beta', '1.3.0-beta.2') === true, 'beta accepts 1.3.0-beta.2');
parisii_assert(parisii_updates_is_channel_version('beta', '1.3.0') === false, 'beta rejects a stable version');
parisii_assert(parisii_updates_is_newer('1.2.3', '1.3.0-beta.1') === true, 'a beta preview is newer than the previous stable');
parisii_assert(parisii_updates_is_newer('1.3.0-beta.1', '1.3.0-beta.2') === true, 'beta numbers increase');
parisii_assert(parisii_updates_is_newer('1.3.0', '1.3.0-beta.2') === false, 'a beta is not newer than its stable release');
$package = parisii_updates_package_url('stable', 'parisii-optique', '1.3.0');
parisii_assert($package === 'https://updates.phangwilly.com/stable/parisii-optique-1.3.0.zip', 'theme package URL stays on the stable channel');
$plugin_package = parisii_updates_package_url('beta', 'parisii-optique-plugin', '1.3.0-beta.1');
parisii_assert($plugin_package === 'https://updates.phangwilly.com/beta/parisii-optique-plugin-1.3.0-beta.1.zip', 'plugin package URL stays on the beta channel');
parisii_assert(parisii_updates_package_is_allowed('stable', 'parisii-optique', '1.3.0', $package) === true, 'the expected package URL is accepted');
parisii_assert(parisii_updates_package_is_allowed('stable', 'parisii-optique', '1.3.0', 'https://example.test/stable/parisii-optique-1.3.0.zip') === false, 'another host is rejected');
parisii_assert(parisii_updates_offer_is_installable('beta', '1.0.0', array(
    'slug' => 'parisii-optique-plugin',
    'version' => '1.0.1-beta.1',
    'package' => parisii_updates_package_url('beta', 'parisii-optique-plugin', '1.0.1-beta.1'),
)) === true, 'a newer plugin beta can be offered');
parisii_assert(parisii_updates_offer_is_installable('stable', '1.0.0', array(
    'slug' => 'parisii-optique',
    'version' => '1.0.1-beta.1',
    'package' => parisii_updates_package_url('stable', 'parisii-optique', '1.0.1-beta.1'),
)) === false, 'production does not offer a beta package');

if ($failures > 0) {
    fwrite(STDERR, $failures . " test(s) failed\n");
    exit(1);
}

echo "All helper tests passed\n";
exit(0);
