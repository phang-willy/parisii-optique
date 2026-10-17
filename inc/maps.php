<?php
/**
 * Google Maps embed rendering.
 *
 * The theme only prints an iframe. It does not load the Maps JavaScript API.
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/helpers-pure.php';

/**
 * Sanitize the Customizer embed down to one Maps iframe.
 *
 * @param string $html Raw HTML.
 * @return string
 */
function parisii_optique_sanitize_google_maps_embed($html) {
    $iframe = parisii_optique_prepare_maps_iframe_html((string) $html);
    if ($iframe === '') {
        return '';
    }

    return wp_kses($iframe, [
        'iframe' => [
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'style'           => true,
            'allowfullscreen' => true,
            'loading'         => true,
            'referrerpolicy'  => true,
            'title'           => true,
        ],
    ]);
}

/**
 * Address used for the external Maps link.
 *
 * @return string
 */
function parisii_optique_maps_link() {
    $street = (string) get_theme_mod('address_street', '12 Véloroute Sequana');
    $city = (string) get_theme_mod('address_city', '95240 Cormeilles-en-Parisis');

    return parisii_optique_maps_external_url($street, $city);
}

/**
 * Sanitized iframe stored in the Customizer.
 *
 * @return string
 */
function parisii_optique_maps_iframe_markup() {
    return parisii_optique_sanitize_google_maps_embed((string) get_theme_mod('google_maps_embed', ''));
}

/**
 * Print the external Maps link.
 */
function parisii_optique_render_maps_link() {
    $link = parisii_optique_maps_link();
    if ($link === '') {
        return;
    }

    echo '<p class="mt-2"><a class="underline" href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">';
    echo esc_html('Ouvrir dans Google Maps');
    echo '</a></p>';
}

/**
 * Print the map iframe and a link that stays available if the iframe is blocked.
 */
function parisii_optique_render_maps_embed() {
    $iframe = parisii_optique_maps_iframe_markup();

    echo '<div class="parisii-maps w-full" style="aspect-ratio: 4 / 3;">';
    if ($iframe !== '') {
        echo $iframe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized iframe.
    } else {
        echo '<p>' . esc_html("La carte n'est pas disponible.") . '</p>';
    }
    parisii_optique_render_maps_link();
    echo '</div>';
}
