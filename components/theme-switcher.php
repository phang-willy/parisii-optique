<?php
/**
 * Theme Switcher Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Switcher shortcode
 */
function parisii_optique_theme_switcher_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 'theme-switcher-' . uniqid(),
        'style' => 'button', // button, toggle, select, dropdown
        'size' => 'medium', // small, medium, large
        'show_label' => 'true',
        'label_text' => 'Thème',
        'class' => '',
    ], $atts);

    $show_label = $atts['show_label'] === 'true';
    $size_classes = [
        'small' => 'w-8 h-8 text-sm',
        'medium' => 'w-10 h-10 text-base',
        'large' => 'w-12 h-12 text-lg',
    ];

    ob_start();
    ?>
    <div id="<?php echo esc_attr($atts['id']); ?>" class="theme-switcher-component <?php echo esc_attr($atts['class']); ?>">
        <?php if ($show_label) : ?>
            <label for="<?php echo esc_attr($atts['id']); ?>-input" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <?php echo esc_html($atts['label_text']); ?>
            </label>
        <?php endif; ?>
        
        <?php if ($atts['style'] === 'button') : ?>
            <button id="<?php echo esc_attr($atts['id']); ?>-input" 
                    class="theme-switcher-btn <?php echo esc_attr($size_classes[$atts['size']]); ?> p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-main-500 border-gray-400 border dark:hover:text-gray-700" 
                    aria-label="<?php esc_attr_e('Changer de thème', 'parisii-optique'); ?>"
                    title="<?php esc_attr_e('Changer de thème', 'parisii-optique'); ?>">
                <svg class="sun-icon w-full h-full transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <svg class="moon-icon w-full h-full transition-all duration-300 absolute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>
            
        <?php elseif ($atts['style'] === 'dropdown') : ?>
            <div class="relative">
                <button id="<?php echo esc_attr($atts['id']); ?>-input" 
                        class="theme-switcher-btn <?php echo esc_attr($size_classes[$atts['size']]); ?> p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 transition-colors focus:outline-none focus:ring-2 focus:ring-main-500 border-gray-400 border dark:hover:text-gray-700" 
                        aria-label="<?php esc_attr_e('Changer de thème', 'parisii-optique'); ?>"
                        title="<?php esc_attr_e('Changer de thème', 'parisii-optique'); ?>"
                        data-dropdown-toggle="<?php echo esc_attr($atts['id']); ?>-dropdown">
                    <svg class="sun-icon w-full h-full transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <svg class="moon-icon w-full h-full transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg class="system-icon w-full h-full transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </button>
                
                <div id="<?php echo esc_attr($atts['id']); ?>-dropdown" 
                     class="theme-dropdown-menu absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 hidden">
                    <div>
                        <button class="theme-dropdown-item w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center rounded-tl-md rounded-tr-md" 
                                data-theme="light">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Clair
                        </button>
                        <button class="theme-dropdown-item w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center" 
                                data-theme="dark">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                            Sombre
                        </button>
                        <button class="theme-dropdown-item w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center rounded-bl-md rounded-br-md" 
                                data-theme="system">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Appareil
                        </button>
                    </div>
                </div>
            </div>
            
        <?php elseif ($atts['style'] === 'toggle') : ?>
            <div class="relative inline-block">
                <input type="checkbox" 
                       id="<?php echo esc_attr($atts['id']); ?>-input" 
                       class="theme-switcher-toggle sr-only">
                <label for="<?php echo esc_attr($atts['id']); ?>-input" 
                       class="flex items-center cursor-pointer">
                    <div class="relative">
                        <div class="w-14 h-7 bg-gray-200 dark:bg-gray-700 rounded-full shadow-inner transition-colors duration-300 border-gray-400 border"></div>
                        <div class="absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full shadow transform transition-transform duration-300 theme-switcher-toggle-handle">
                            <svg class="w-4 h-4 m-1 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </div>
                    </div>
                </label>
            </div>
            
        <?php elseif ($atts['style'] === 'select') : ?>
            <select id="<?php echo esc_attr($atts['id']); ?>-input" 
                    class="theme-switcher-select block w-full px-3 py-2 border border-gray-300 dark:border-gray-400 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-main-500 focus:border-main-500">
                <option value="light"><?php esc_html_e('Clair', 'parisii-optique'); ?></option>
                <option value="dark"><?php esc_html_e('Sombre', 'parisii-optique'); ?></option>
                <option value="system"><?php esc_html_e('Système', 'parisii-optique'); ?></option>
            </select>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('theme_switcher', 'parisii_optique_theme_switcher_shortcode');

/**
 * Theme Switcher Widget
 */
class Parisii_Theme_Switcher_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'parisii_theme_switcher',
            __('Sélecteur de Thème', 'parisii-optique'),
            ['description' => __('Affiche un sélecteur de thème personnalisable', 'parisii-optique')]
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $style = !empty($instance['style']) ? $instance['style'] : 'button';
        $size = !empty($instance['size']) ? $instance['size'] : 'medium';
        $show_label = !empty($instance['show_label']) ? $instance['show_label'] : 'true';
        $label_text = !empty($instance['label_text']) ? $instance['label_text'] : 'Thème';
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        echo do_shortcode("[theme_switcher style='{$style}' size='{$size}' show_label='{$show_label}' label_text='{$label_text}']");
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $style = !empty($instance['style']) ? $instance['style'] : 'button';
        $size = !empty($instance['size']) ? $instance['size'] : 'medium';
        $show_label = !empty($instance['show_label']) ? $instance['show_label'] : 'true';
        $label_text = !empty($instance['label_text']) ? $instance['label_text'] : 'Thème';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Titre:', 'parisii-optique'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Style:', 'parisii-optique'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('style'); ?>" name="<?php echo $this->get_field_name('style'); ?>">
                <option value="button" <?php selected($style, 'button'); ?>><?php _e('Bouton', 'parisii-optique'); ?></option>
                <option value="toggle" <?php selected($style, 'toggle'); ?>><?php _e('Toggle', 'parisii-optique'); ?></option>
                <option value="select" <?php selected($style, 'select'); ?>><?php _e('Sélecteur', 'parisii-optique'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('size'); ?>"><?php _e('Taille:', 'parisii-optique'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">
                <option value="small" <?php selected($size, 'small'); ?>><?php _e('Petit', 'parisii-optique'); ?></option>
                <option value="medium" <?php selected($size, 'medium'); ?>><?php _e('Moyen', 'parisii-optique'); ?></option>
                <option value="large" <?php selected($size, 'large'); ?>><?php _e('Grand', 'parisii-optique'); ?></option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('show_label'); ?>">
                <input type="checkbox" id="<?php echo $this->get_field_id('show_label'); ?>" name="<?php echo $this->get_field_name('show_label'); ?>" value="true" <?php checked($show_label, 'true'); ?>>
                <?php _e('Afficher le label', 'parisii-optique'); ?>
            </label>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('label_text'); ?>"><?php _e('Texte du label:', 'parisii-optique'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('label_text'); ?>" name="<?php echo $this->get_field_name('label_text'); ?>" type="text" value="<?php echo esc_attr($label_text); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['style'] = (!empty($new_instance['style'])) ? sanitize_text_field($new_instance['style']) : 'button';
        $instance['size'] = (!empty($new_instance['size'])) ? sanitize_text_field($new_instance['size']) : 'medium';
        $instance['show_label'] = (!empty($new_instance['show_label'])) ? 'true' : 'false';
        $instance['label_text'] = (!empty($new_instance['label_text'])) ? sanitize_text_field($new_instance['label_text']) : 'Thème';
        return $instance;
    }
}

// Register the widget
function parisii_register_theme_switcher_widget() {
    register_widget('Parisii_Theme_Switcher_Widget');
}
add_action('widgets_init', 'parisii_register_theme_switcher_widget');

/**
 * Enqueue theme switcher scripts
 */
function parisii_theme_switcher_scripts() {
    // Always enqueue theme switcher scripts for better performance
    wp_enqueue_script('parisii-theme-switcher', get_template_directory_uri() . '/js/theme-switcher.js', [], '1.0.0', true);
    wp_enqueue_style('parisii-theme-switcher', get_template_directory_uri() . '/css/theme-switcher.css', [], '1.0.0');
}
add_action('wp_enqueue_scripts', 'parisii_theme_switcher_scripts');
