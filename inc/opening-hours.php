<?php
/**
 * Opening hours access for templates and structured data.
 *
 * Values already stored in WordPress are never overwritten here.
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/helpers-pure.php';

/**
 * Raw theme mods for the active stylesheet.
 *
 * @return array<string,mixed>
 */
function parisii_optique_theme_mods() {
    $mods = get_theme_mods();
    if (!is_array($mods)) {
        $mods = [];
    }

    if (is_customize_preview() && isset($GLOBALS['wp_customize']) && $GLOBALS['wp_customize'] instanceof WP_Customize_Manager) {
        foreach (['hours_weekdays', 'hours_monday', 'hours_tuesday_friday', 'hours_saturday', 'hours_sunday', 'hours_exceptional'] as $key) {
            $setting = $GLOBALS['wp_customize']->get_setting($key);
            if ($setting instanceof WP_Customize_Setting) {
                $posted = $setting->post_value(null);
                if ($posted !== null) {
                    $mods[$key] = $posted;
                }
            }
        }
    }

    return $mods;
}

/**
 * Resolved opening hours for the current site.
 *
 * @return array<string,array{label:string,closed:bool,opens:string,closes:string,schema:bool}>
 */
function parisii_optique_get_opening_hours() {
    return parisii_optique_resolve_opening_hours(parisii_optique_theme_mods());
}

/**
 * Optional exceptional hours note. Empty when the shop has not set one.
 *
 * @return string
 */
function parisii_optique_get_exceptional_hours() {
    return trim((string) get_theme_mod('hours_exceptional', ''));
}

/**
 * Whether Monday still inherits a custom legacy weekday value.
 *
 * @return bool
 */
function parisii_optique_opening_hours_need_split() {
    $mods = parisii_optique_theme_mods();
    if (array_key_exists('hours_monday', $mods) || array_key_exists('hours_tuesday_friday', $mods)) {
        return false;
    }

    $legacy = trim((string) ($mods['hours_weekdays'] ?? ''));

    return $legacy !== '' && !parisii_optique_is_legacy_weekday_default($legacy);
}

/**
 * Print the opening hours list.
 *
 * @param string $context footer or home.
 */
function parisii_optique_render_opening_hours($context = 'footer') {
    $rows = parisii_optique_opening_hours_rows(parisii_optique_get_opening_hours());
    $exceptional = parisii_optique_get_exceptional_hours();

    if ($context === 'home') {
        echo '<ul>';
        foreach ($rows as $row) {
            echo '<li>' . esc_html($row['label'] . ' : ' . $row['value']) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="flex flex-col gap-2 text-gray-700 dark:text-gray-300">';
        foreach ($rows as $row) {
            echo '<div class="flex justify-between gap-4">';
            echo '<span>' . esc_html($row['label']) . '</span>';
            echo '<span>' . esc_html($row['value']) . '</span>';
            echo '</div>';
        }
        echo '</div>';
    }

    if ($exceptional !== '') {
        echo '<p class="mt-3">' . esc_html($exceptional) . '</p>';
    }
}

/**
 * Ask an administrator to split a custom legacy weekday range.
 */
function parisii_optique_opening_hours_admin_notice() {
    if (!current_user_can('manage_options') || !parisii_optique_opening_hours_need_split()) {
        return;
    }

    echo '<div class="notice notice-warning"><p><strong>Parisii Optique :</strong> ';
    echo esc_html("Les horaires du lundi et du mardi au vendredi reprennent encore l'ancien champ unique, parce qu'il contient une valeur personnalisée. Ouvrez Apparence > Personnaliser > Informations > Horaires pour enregistrer les deux champs séparément. Rien n'a été écrasé.");
    echo '</p></div>';
}
add_action('admin_notices', 'parisii_optique_opening_hours_admin_notice');

/**
 * Remind administrators to review the sample page without deleting it.
 */
function parisii_optique_sample_page_admin_notice() {
    if (!current_user_can('manage_options') || !function_exists('get_page_by_path')) {
        return;
    }

    $slugs = ['page-d-exemple', 'sample-page'];
    $titles = [];
    foreach ($slugs as $slug) {
        $page = get_page_by_path($slug);
        if ($page instanceof WP_Post && $page->post_status === 'publish') {
            $titles[] = $page->post_title . ' (/' . $slug . '/)';
        }
    }

    if (!$titles) {
        return;
    }

    echo '<div class="notice notice-info"><p><strong>Parisii Optique :</strong> ';
    echo esc_html('Pages à valider avant indexation : ' . implode(', ', $titles) . '. Elles restent publiées et présentes dans le sitemap tant que vous ne les dépubliez pas.');
    echo '</p></div>';
}
add_action('admin_notices', 'parisii_optique_sample_page_admin_notice');
