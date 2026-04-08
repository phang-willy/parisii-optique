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
     * Get filtered brands
     */
    public static function get_filtered_brands($props = array()) {
        $props = wp_parse_args($props, array(
            'kids' => null, // true => kids only, false/null => all
        ));

        $args = array(
            'visible_only' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        );

        if ($props['kids'] === true) {
            $args['kids'] = 1;
        }
        
        // Search filter
        if (!empty($_GET['brand_search'])) {
            $args['search'] = sanitize_text_field($_GET['brand_search']);
        }
        
        return Parisii_Optique_Brand::get_all($args);
    }
    
    /**
     * Render filter sidebar
     */
    public static function render_filter_sidebar() {
        $search_query = isset($_GET['brand_search']) ? sanitize_text_field($_GET['brand_search']) : '';
        
        ?>
        <aside class="w-full">
            <div class="card lg:sticky lg:top-24">
                <h3 class="text-lg font-heading font-semibold mb-4"><?php _e('Filtrer les marques', 'parisii-optique-plugin'); ?></h3>
                
                <form method="get">
                    <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">
                    
                    <div class="mb-4">
                        <label for="brand_search" class="block text-sm font-medium mb-2">
                            <?php _e('Rechercher', 'parisii-optique-plugin'); ?>
                        </label>
                        <input 
                            type="search" 
                            id="brand_search" 
                            name="brand_search" 
                            value="<?php echo esc_attr($search_query); ?>"
                            placeholder="<?php esc_attr_e('Nom de la marque...', 'parisii-optique-plugin'); ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 bg-secondary bg-secondary dark:bg-gray-800 dark:border-gray-700 dark:text-white"
                        >
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-secondary bg-secondary text-white rounded-lg font-medium transition-colors duration-200 focus:ring-2 bg-secondary focus:ring-offset-2">
                            <?php _e('Filtrer', 'parisii-optique-plugin'); ?>
                        </button>
                        <a href="<?php echo get_permalink(); ?>" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white text-gray-900 text-center rounded-lg font-medium transition-colors duration-200">
                            <?php _e('Réinitialiser', 'parisii-optique-plugin'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </aside>
        <?php
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
                            class="w-full mx-auto rounded-lg overflow-hidden bg-white dark:bg-black flex items-center justify-center max-h-[125px] min-h-[60px] aspect-ratio-220-125"
                        >
                            <img 
                                src="<?php echo esc_url($brand->logo); ?>" 
                                alt="<?php echo esc_attr($brand->name); ?>" 
                                class="max-h-full max-w-full object-contain w-auto h-auto aspect-ratio-220-125"
                                loading="lazy"
                            >
                        </div>
                        <figcaption class="sr-only"><?php echo esc_html($brand->name); ?></figcaption>
                    </figure>
                <?php endif; ?>
                <div class="flex-grow">
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
        $brands = self::get_filtered_brands($props);
        
        if (empty($brands)) {
            echo '<div class="card text-center py-12 bg-gray-50 dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-700">';
            echo '<div class="text-gray-400 dark:text-gray-500 mb-4">';
            echo '<svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>';
            echo '</svg>';
            echo '</div>';
            echo '<p class="text-gray-500 dark:text-gray-400 text-lg">' . __('Aucune marque trouvée.', 'parisii-optique-plugin') . '</p>';
            echo '<p class="text-gray-400 dark:text-gray-500 text-sm mt-2">' . __('Essayez de modifier vos critères de recherche.', 'parisii-optique-plugin') . '</p>';
            echo '</div>';
            return;
        }
        
        echo '<div class="card-layout md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';
        foreach ($brands as $brand) {
            self::render_brand_card($brand);
        }
        echo '</div>';
    }
}

