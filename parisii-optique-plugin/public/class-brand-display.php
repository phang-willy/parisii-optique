<?php
/**
 * Brand Display class
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Brand_Display {
    
    public function __construct() {
        // No hooks needed yet, methods will be called directly from template
    }
    
    /**
     * Pixel size of a brand logo, used so lazy images keep their box.
     *
     * @param string $url Logo URL.
     * @return array{0:int,1:int}
     */
    private static function logo_dimensions($url) {
        $width = 220;
        $height = 125;
        $url = (string) $url;
        $attachment_id = attachment_url_to_postid($url);

        if ($attachment_id) {
            $meta = wp_get_attachment_metadata($attachment_id);
            if (!empty($meta['width']) && !empty($meta['height'])) {
                return array((int) $meta['width'], (int) $meta['height']);
            }
        }

        $uploads = wp_get_upload_dir();
        if (!empty($uploads['baseurl']) && !empty($uploads['basedir']) && str_starts_with($url, $uploads['baseurl'])) {
            $relative = ltrim(substr($url, strlen($uploads['baseurl'])), '/');
            $path = $uploads['basedir'] . '/' . $relative;
            if (is_file($path)) {
                $size = getimagesize($path);
                if (is_array($size) && !empty($size[0]) && !empty($size[1])) {
                    return array((int) $size[0], (int) $size[1]);
                }
            }
        }

        return array($width, $height);
    }

    /**
     * Render brand card
     */
    public static function render_brand_card($brand) {
        $logo_width = 220;
        $logo_height = 125;
        if (!empty($brand->logo)) {
            list($logo_width, $logo_height) = self::logo_dimensions($brand->logo);
        }
        ?>
        <article class="card parisii-optique-brand-card flex flex-col h-full hover:shadow-lg dark:hover:shadow-lg dark:shadow-md transition-shadow duration-300">
            <section class="content flex flex-col gap-4 h-full">
                <?php if (!empty($brand->logo)) : ?>
                    <figure class="flex items-center justify-center rounded-lg">
                        <div
                            class="w-full mx-auto rounded-lg overflow-hidden bg-white dark:bg-black flex items-center justify-center max-h-31.25 min-h-15 aspect-ratio-220-125"
                            style="aspect-ratio: 220 / 125;"
                        >
                            <img
                                src="<?php echo esc_url($brand->logo); ?>"
                                alt="Retrouver <?php echo esc_attr($brand->name); ?> chez <?php echo esc_attr(get_bloginfo('name')); ?>"
                                class="h-full w-full max-h-full max-w-full object-contain"
                                width="<?php echo esc_attr((string) $logo_width); ?>"
                                height="<?php echo esc_attr((string) $logo_height); ?>"
                                style="aspect-ratio: <?php echo esc_attr((string) $logo_width); ?> / <?php echo esc_attr((string) $logo_height); ?>;"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                        <figcaption class="sr-only">Retrouver <?php echo esc_html($brand->name); ?> chez <?php echo esc_html(get_bloginfo('name')); ?></figcaption>
                    </figure>
                <?php endif; ?>
                <div class="grow">
                    <h3 class="text-xl font-heading font-semibold text-gray-900 dark:text-white">
                        <?php echo esc_html($brand->name); ?>
                    </h3>
                </div>
            </section>
        </article>
        <?php
    }
    
    /**
     * Render brands grid
     */
    public static function render_brands_grid($props = array()) {
        $props = wp_parse_args(
            $props,
            array(
                'kids' => null,
            )
        );

        $args = array(
            'visible_only' => true,
            'orderby'      => 'name',
            'order'        => 'ASC',
        );

        if ($props['kids'] === true) {
            $args['kids'] = 1;
        }

        $brands = Parisii_Optique_Brand::get_all($args);
        
        echo '<div class="card-layout md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';
        foreach ($brands as $brand) {
            self::render_brand_card($brand);
        }
        echo '</div>';
    }
}
