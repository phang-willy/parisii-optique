<?php
/**
 * Pure helpers shared by the theme and the CLI tests.
 *
 * No WordPress calls: this file must stay executable without loading WordPress.
 *
 * @package Parisii_Optique
 */

/**
 * Normalize a free-text opening range for comparison.
 *
 * @param string $value Raw hours text.
 * @return string
 */
function parisii_optique_normalize_hours_text($value) {
    $value = str_replace(
        ["\xC2\xA0", '–', '—', '−'],
        [' ', '-', '-', '-'],
        (string) $value
    );
    $value = trim((string) preg_replace('/\s+/', ' ', $value));
    $value = (string) preg_replace_callback(
        '/(?<!\d)(\d)h/',
        static function ($matches) {
            return '0' . $matches[1] . 'h';
        },
        $value
    );

    return $value;
}

/**
 * Whether a weekday value is still the historical Customizer default.
 *
 * @param string $value Raw hours text.
 * @return bool
 */
function parisii_optique_is_legacy_weekday_default($value) {
    $normalized = str_replace(' ', '', parisii_optique_normalize_hours_text($value));

    return in_array($normalized, ['09h30-19h00'], true);
}

/**
 * Parse one opening range.
 *
 * Closed days and unparsed text never invent schema times.
 *
 * @param string $value Raw hours text.
 * @return array{closed:bool,opens:string,closes:string,label:string,schema:bool}
 */
function parisii_optique_parse_opening_hours($value) {
    $label = trim((string) $value);
    if ($label === '' || preg_match('/ferm[eé]|closed/i', $label)) {
        return [
            'closed' => true,
            'opens'  => '',
            'closes' => '',
            'label'  => $label !== '' ? $label : 'Fermé',
            'schema' => false,
        ];
    }

    if (preg_match('/(\d{1,2})\s*h\s*(\d{2})?\s*-\s*(\d{1,2})\s*h\s*(\d{2})?/iu', parisii_optique_normalize_hours_text($label), $matches)) {
        return [
            'closed' => false,
            'opens'  => sprintf('%02d:%02d', (int) $matches[1], $matches[2] !== '' ? (int) $matches[2] : 0),
            'closes' => sprintf('%02d:%02d', (int) $matches[3], $matches[4] !== '' ? (int) $matches[4] : 0),
            'label'  => $label,
            'schema' => true,
        ];
    }

    return [
        'closed' => false,
        'opens'  => '',
        'closes' => '',
        'label'  => $label,
        'schema' => false,
    ];
}

/**
 * Resolve opening hours from raw theme mods without writing anything.
 *
 * A custom legacy "hours_weekdays" value is kept for Monday and Tuesday-Friday
 * until the new keys are saved. The historical default is replaced in memory
 * by the official split hours.
 *
 * @param array<string,mixed> $mods Raw theme mods.
 * @return array<string,array{label:string,closed:bool,opens:string,closes:string,schema:bool}>
 */
function parisii_optique_resolve_opening_hours(array $mods) {
    $legacy = array_key_exists('hours_weekdays', $mods) ? trim((string) $mods['hours_weekdays']) : '';
    $legacy_is_custom = $legacy !== '' && !parisii_optique_is_legacy_weekday_default($legacy);

    $monday = array_key_exists('hours_monday', $mods) ? trim((string) $mods['hours_monday']) : '';
    if ($monday === '') {
        $monday = $legacy_is_custom ? $legacy : '14h00 - 19h00';
    }

    $tuesday_friday = array_key_exists('hours_tuesday_friday', $mods) ? trim((string) $mods['hours_tuesday_friday']) : '';
    if ($tuesday_friday === '') {
        $tuesday_friday = $legacy_is_custom ? $legacy : '09h30 - 19h00';
    }

    $saturday = array_key_exists('hours_saturday', $mods) ? trim((string) $mods['hours_saturday']) : '';
    if ($saturday === '') {
        $saturday = '10h00 - 19h30';
    }

    $sunday = array_key_exists('hours_sunday', $mods) ? trim((string) $mods['hours_sunday']) : '';
    if ($sunday === '') {
        $sunday = 'Fermé';
    }

    $days = [
        'monday'    => $monday,
        'tuesday'   => $tuesday_friday,
        'wednesday' => $tuesday_friday,
        'thursday'  => $tuesday_friday,
        'friday'    => $tuesday_friday,
        'saturday'  => $saturday,
        'sunday'    => $sunday,
    ];

    $resolved = [];
    foreach ($days as $day => $text) {
        $parsed = parisii_optique_parse_opening_hours($text);
        $resolved[$day] = [
            'label'  => $parsed['label'],
            'closed' => $parsed['closed'],
            'opens'  => $parsed['opens'],
            'closes' => $parsed['closes'],
            'schema' => $parsed['schema'],
        ];
    }

    return $resolved;
}

/**
 * Group resolved hours for visible lists.
 *
 * @param array<string,array{label:string}> $hours Resolved hours.
 * @return array<int,array{label:string,value:string}>
 */
function parisii_optique_opening_hours_rows(array $hours) {
    $monday = $hours['monday']['label'] ?? '';
    $weekdays = $hours['tuesday']['label'] ?? '';
    $rows = [];

    if ($monday !== '' && $monday === $weekdays) {
        $rows[] = [
            'label' => 'Lundi - vendredi',
            'value' => $monday,
        ];
    } else {
        $rows[] = [
            'label' => 'Lundi',
            'value' => $monday,
        ];
        $rows[] = [
            'label' => 'Mardi - vendredi',
            'value' => $weekdays,
        ];
    }

    $rows[] = [
        'label' => 'Samedi',
        'value' => $hours['saturday']['label'] ?? '',
    ];
    $rows[] = [
        'label' => 'Dimanche',
        'value' => $hours['sunday']['label'] ?? '',
    ];

    return $rows;
}

/**
 * Build schema.org openingHoursSpecification entries.
 *
 * @param array<string,array{closed:bool,opens:string,closes:string,schema:bool}> $hours Resolved hours.
 * @return array<int,array<string,mixed>>
 */
function parisii_optique_opening_hours_schema(array $hours) {
    $groups = [];

    foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
        if (empty($hours[$day]['schema']) || !empty($hours[$day]['closed'])) {
            continue;
        }

        $key = $hours[$day]['opens'] . '|' . $hours[$day]['closes'];
        if (!isset($groups[$key])) {
            $groups[$key] = [
                'opens'  => $hours[$day]['opens'],
                'closes' => $hours[$day]['closes'],
                'days'   => [],
            ];
        }
        $groups[$key]['days'][] = ucfirst($day);
    }

    $specifications = [];
    foreach ($groups as $group) {
        $specifications[] = [
            '@type'     => 'OpeningHoursSpecification',
            'dayOfWeek' => $group['days'],
            'opens'     => $group['opens'],
            'closes'    => $group['closes'],
        ];
    }

    return $specifications;
}

/**
 * Split a stored city line such as "95240 Cormeilles-en-Parisis".
 *
 * @param string $city_line City line.
 * @return array{postal_code:string,locality:string}
 */
function parisii_optique_split_city_line($city_line) {
    $city_line = trim((string) $city_line);
    $postal_code = '';
    $locality = $city_line;

    if (preg_match('/\b(\d{5})\b/', $city_line, $matches)) {
        $postal_code = $matches[1];
        $locality = trim((string) preg_replace('/\b' . preg_quote($postal_code, '/') . '\b/', '', $city_line));
        $locality = trim((string) preg_replace('/\s+/', ' ', $locality), " \t\n\r\0\x0B,");
    }

    return [
        'postal_code' => $postal_code,
        'locality'    => $locality,
    ];
}

/**
 * Whether a Maps iframe src is an HTTPS Google Maps embed.
 *
 * @param string $src Iframe src.
 * @return bool
 */
function parisii_optique_maps_src_is_allowed($src) {
    $parts = parse_url((string) $src);
    if (!is_array($parts) || empty($parts['scheme']) || strtolower((string) $parts['scheme']) !== 'https') {
        return false;
    }

    $host = strtolower((string) ($parts['host'] ?? ''));
    $allowed_hosts = [
        'www.google.com',
        'google.com',
        'maps.google.com',
        'www.google.fr',
        'google.fr',
    ];
    if (!in_array($host, $allowed_hosts, true)) {
        return false;
    }

    $path = (string) ($parts['path'] ?? '');

    return str_starts_with($path, '/maps');
}

/**
 * Read the first iframe src from an embed snippet.
 *
 * @param string $html Embed HTML.
 * @return string
 */
function parisii_optique_maps_iframe_src($html) {
    if (!preg_match('/<iframe\b[^>]*\bsrc\s*=\s*(["\'])([^"\']+)\1/i', (string) $html, $matches)) {
        return '';
    }

    return html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8');
}

/**
 * Keep a limited CSS declaration set for the map iframe.
 *
 * @param string $style Style attribute.
 * @return string
 */
function parisii_optique_sanitize_iframe_style($style) {
    $style = trim((string) $style);
    if ($style === '' || preg_match('/url\s*\(|expression|javascript|@import/i', $style)) {
        return 'border:0;';
    }
    if (!preg_match('/^[a-z0-9:%;\s.\-]+$/i', $style)) {
        return 'border:0;';
    }

    return $style;
}

/**
 * Rebuild a single safe Google Maps iframe.
 *
 * @param string $html Raw embed HTML.
 * @return string
 */
function parisii_optique_prepare_maps_iframe_html($html) {
    $src = parisii_optique_maps_iframe_src($html);
    if (!parisii_optique_maps_src_is_allowed($src)) {
        return '';
    }

    $width = '';
    $height = '';
    $style = 'border:0;';
    $title = 'Carte Parisii Optique';
    $loading = 'lazy';
    $referrer = 'strict-origin-when-cross-origin';

    if (preg_match('/\bwidth\s*=\s*(["\'])([^"\']+)\1/i', $html, $matches) && preg_match('/^\d{2,4}%?$/', $matches[2])) {
        $width = $matches[2];
    }
    if (preg_match('/\bheight\s*=\s*(["\'])([^"\']+)\1/i', $html, $matches) && preg_match('/^\d{2,4}%?$/', $matches[2])) {
        $height = $matches[2];
    }
    if (preg_match('/\bstyle\s*=\s*(["\'])([^"\']+)\1/i', $html, $matches)) {
        $style = parisii_optique_sanitize_iframe_style(html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8'));
    }
    if ($width === '' && !preg_match('/(?:^|;)\s*width\s*:/i', $style)) {
        $style = rtrim($style, ';') . ';width:100%;';
    }
    if ($height === '' && !preg_match('/(?:^|;)\s*height\s*:/i', $style)) {
        $style = rtrim($style, ';') . ';height:100%;';
    }
    if ($width === '') {
        $width = '600';
    }
    if ($height === '') {
        $height = '450';
    }
    if (preg_match('/\btitle\s*=\s*(["\'])([^"\']+)\1/i', $html, $matches)) {
        $custom_title = trim(html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8'));
        if ($custom_title !== '') {
            $title = $custom_title;
        }
    }
    if (preg_match('/\bloading\s*=\s*(["\'])(lazy|eager)\1/i', $html, $matches)) {
        $loading = strtolower($matches[2]);
    }
    if (preg_match('/\breferrerpolicy\s*=\s*(["\'])([a-z\-]+)\1/i', $html, $matches)) {
        $referrer = strtolower($matches[2]);
    }

    $attributes = [
        'src'             => $src,
        'style'           => $style,
        'loading'         => $loading,
        'referrerpolicy'  => $referrer,
        'title'           => $title,
        'allowfullscreen' => '',
    ];
    if ($width !== '') {
        $attributes['width'] = $width;
    }
    if ($height !== '') {
        $attributes['height'] = $height;
    }

    $html_attributes = '';
    foreach ($attributes as $name => $value) {
        if ($name === 'allowfullscreen') {
            $html_attributes .= ' allowfullscreen';
            continue;
        }
        $html_attributes .= ' ' . $name . '="' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') . '"';
    }

    return '<iframe' . $html_attributes . '></iframe>';
}

/**
 * Coordinates embedded by Google in a Maps embed URL.
 *
 * Returns null when the snippet has no verified pair. Nothing is hardcoded.
 *
 * @param string $src Iframe src.
 * @return array{latitude:float,longitude:float}|null
 */
function parisii_optique_maps_embed_coordinates($src) {
    if (!preg_match('/!2d(-?\d+(?:\.\d+)?)!3d(-?\d+(?:\.\d+)?)/', (string) $src, $matches)) {
        return null;
    }

    $longitude = (float) $matches[1];
    $latitude = (float) $matches[2];
    if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
        return null;
    }

    return [
        'latitude'  => $latitude,
        'longitude' => $longitude,
    ];
}

/**
 * Public Google Maps link built from the visible address.
 *
 * @param string $street Street.
 * @param string $city   City line.
 * @return string
 */
function parisii_optique_maps_external_url($street, $city) {
    $query = trim($street . ' ' . $city);
    if ($query === '') {
        return '';
    }

    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query);
}

/**
 * Relative placeholder files, preferred path first.
 *
 * The dated production file is only a transition fallback.
 *
 * @return string[]
 */
function parisii_optique_placeholder_candidates() {
    return [
        'woocommerce-placeholder.webp',
        '2026/02/woocommerce-placeholder-1024x1024-1.webp',
        'woocommerce-placeholder-1024x1024.webp',
    ];
}

/**
 * Pick the first placeholder that exists in the current uploads directory.
 *
 * @param string[] $available Relative paths known to exist.
 * @return string
 */
function parisii_optique_choose_placeholder_relative(array $available) {
    foreach (parisii_optique_placeholder_candidates() as $candidate) {
        if (in_array($candidate, $available, true)) {
            return $candidate;
        }
    }

    return '';
}

/**
 * Sized files used in srcset when the uniform root placeholder exists.
 *
 * @return array<string,int>
 */
function parisii_optique_placeholder_srcset_files() {
    return [
        'woocommerce-placeholder-100x100.webp'  => 100,
        'woocommerce-placeholder-150x150.webp'  => 150,
        'woocommerce-placeholder-300x300.webp'  => 300,
        'woocommerce-placeholder-600x600.webp'  => 600,
        'woocommerce-placeholder-768x768.webp'  => 768,
        'woocommerce-placeholder-1024x1024.webp' => 1024,
    ];
}

/**
 * Join an uploads base URL and a relative path. The host comes only from $baseurl.
 *
 * @param string $baseurl  Uploads base URL.
 * @param string $relative Relative path.
 * @return string
 */
function parisii_optique_join_upload_url($baseurl, $relative) {
    return rtrim((string) $baseurl, '/') . '/' . ltrim((string) $relative, '/');
}

/**
 * Build a srcset from sized files that exist next to the uniform placeholder.
 *
 * @param string            $baseurl          Uploads base URL.
 * @param string[]          $available        Relative paths that exist.
 * @param int               $full_width       Intrinsic width of woocommerce-placeholder.webp.
 * @return string
 */
function parisii_optique_build_placeholder_srcset($baseurl, array $available, $full_width) {
    $parts = [];

    foreach (parisii_optique_placeholder_srcset_files() as $file => $width) {
        if (!in_array($file, $available, true)) {
            continue;
        }
        $parts[] = parisii_optique_join_upload_url($baseurl, $file) . ' ' . $width . 'w';
    }

    if ($full_width > 0 && in_array('woocommerce-placeholder.webp', $available, true)) {
        $parts[] = parisii_optique_join_upload_url($baseurl, 'woocommerce-placeholder.webp') . ' ' . (int) $full_width . 'w';
    }

    return implode(', ', $parts);
}

/**
 * Replace the origin of an absolute URL.
 *
 * @param string $url    Absolute URL.
 * @param string $origin Official origin, including scheme.
 * @return string
 */
function parisii_optique_rewrite_origin($url, $origin) {
    $parts = parse_url((string) $url);
    $origin_parts = parse_url((string) $origin);
    if (!is_array($parts) || !is_array($origin_parts) || empty($origin_parts['host'])) {
        return (string) $url;
    }

    $scheme = $origin_parts['scheme'] ?? 'https';
    $path = $parts['path'] ?? '/';
    $query = isset($parts['query']) ? '?' . $parts['query'] : '';

    return $scheme . '://' . $origin_parts['host'] . $path . $query;
}
