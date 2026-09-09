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
     * Render brand card
     */
    public static function render_brand_card($brand) {
        ?>
        <article class="card parisii-optique-brand-card flex flex-col h-full hover:shadow-lg dark:hover:shadow-lg dark:shadow-md transition-shadow duration-300">
            <section class="content flex flex-col gap-4 h-full">
                <?php if (!empty($brand->logo)) : ?>
                    <figure class="flex items-center justify-center rounded-lg">
                        <div 
                            class="w-full mx-auto rounded-lg overflow-hidden bg-white dark:bg-black flex items-center justify-center max-h-31.25 min-h-15 aspect-ratio-220-125"
                        >
                            <img 
                                src="<?php echo esc_url($brand->logo); ?>" 
                                alt="Retrouver <?php echo esc_attr($brand->name); ?> chez <?php echo esc_attr(get_bloginfo('name')); ?>"
                                class="max-h-full max-w-full object-contain w-auto h-auto aspect-ratio-220-125"
                                loading="lazy"
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
