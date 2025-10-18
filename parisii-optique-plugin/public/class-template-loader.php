<?php
/**
 * Template Loader class
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Template_Loader {
    
    public function __construct() {
        add_filter('theme_page_templates', array($this, 'add_page_template'));
        add_filter('template_include', array($this, 'load_page_template'));
    }
    
    /**
     * Add custom page template to WordPress
     */
    public function add_page_template($templates) {
        $templates['template-brands.php'] = __('Page des marques', 'parisii-optique-plugin');
        return $templates;
    }
    
    /**
     * Load custom page template
     */
    public function load_page_template($template) {
        if (is_page()) {
            $page_template = get_post_meta(get_the_ID(), '_wp_page_template', true);
            
            if ($page_template === 'template-brands.php') {
                $plugin_template = PARISII_OPTIQUE_PLUGIN_PATH . 'public/templates/template-brands.php';
                
                if (file_exists($plugin_template)) {
                    return $plugin_template;
                }
            }
        }
        
        return $template;
    }
}

