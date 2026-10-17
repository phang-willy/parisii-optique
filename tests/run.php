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
parisii_assert($closed['schema'] === false && $closed['opens'] === '', 'closed text does not invent opening times');

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

if ($failures > 0) {
    fwrite(STDERR, $failures . " test(s) failed\n");
    exit(1);
}

echo "All helper tests passed\n";
exit(0);
