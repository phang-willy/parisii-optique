<?php
/**
 * Theme functions and utilities
 *
 * @package Parisii_Optique
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom Walker for Desktop Navigation
 */
class Parisii_Optique_Walker_Nav_Menu extends Walker_Nav_Menu {
    
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        // Sous-menu niveau 1 : en dessous (top-full)
        // Sous-menu niveau 2+ : à droite (left-full)
        if ($depth === 0) {
            $output .= "\n$indent<ul class=\"dropdown-menu absolute top-full left-0 mt-2 min-w-48 bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 z-100 py-2\">\n";
        } else {
            $output .= "\n$indent<ul class=\"dropdown-menu hidden absolute top-0 left-full ml-1 min-w-48 bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 z-100 py-2\">\n";
        }
    }
    
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Détecter si c'est la page active
        $is_current = in_array('current-menu-item', $classes);
        $is_current_parent = in_array('current-menu-parent', $classes) || in_array('current-menu-ancestor', $classes);
        
        // Enlever les classes d'icônes
        $classes = array_filter($classes, function($class) {
            return strpos($class, 'dashicons') === false && strpos($class, 'menu-item-icon') === false;
        });
        
        $has_children = in_array('menu-item-has-children', $classes);

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $extra_class = $has_children ? 'li-nav-link-has-children' : 'li-nav-link';
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . ' ' . $extra_class . '"' : ' class="' . $extra_class . '"';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        // Vérifier si l'item a des enfants
        
        // Wrapper pour le contenu de l'item si a des enfants
        $is_active = ($is_current || $is_current_parent);
        $active_wrapper = $is_active ? ' bg-main text-white' : '';
        if ($has_children && $depth === 0) {
            $output .= '<div class="flex items-center gap-8 menu-container pl-4 bg-main bg-secondary hover:text-white transition-colors duration-200' . $active_wrapper . '">';
        } elseif ($has_children && $depth > 0) {
            $output .= '<div class="flex items-center justify-between w-full group bg-main bg-secondary hover:text-white transition-colors duration-200' . $active_wrapper . '">';
        }
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        // Classes différentes pour les liens selon la profondeur et état actif
        $link_classes = "";
        if ($depth === 0) {
            if ($has_children) {
                // Pour les parents niveau 0 : pas de bg, juste le texte
                $text_color = $is_active ? 'text-white' : 'text-gray-700 dark:text-gray-300';
                $link_classes = 'nav-link block ' . $text_color . ' text-sm font-medium transition-colors duration-200';
            } else {
                // Pour les items sans enfants niveau 0 : bg et hover complets
                $active_link_class = $is_active ? 'bg-main text-white' : 'text-gray-700 dark:text-gray-300';
                $link_classes = 'nav-link block ' . $active_link_class . ' hover:bg-main hover:text-white py-3.5 text-sm font-medium px-6 transition-colors duration-200';
            }
        } else {
            // Même style pour tous les sous-niveaux (enfants et enfants d'enfants)
            $active_link_class = $is_active ? 'bg-main text-white' : 'text-gray-700 dark:text-gray-300';
            
            if ($has_children) {
                $link_classes = 'nav-link flex-1 ' . $active_link_class . ' px-4 py-3 text-sm font-medium transition-colors duration-200 group-hover:bg-main group-hover:text-white';
            } else {
                $link_classes = 'nav-link block ' . $active_link_class . ' hover:bg-main hover:text-white px-4 py-3 text-sm font-medium transition-colors duration-200';
            }
        }
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . ' class="' . $link_classes . '">';
        
        // Titre du menu
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        
        $item_output .= '</a>';
        
        // Ajouter bouton toggle ou chevron si l'item a des enfants
        if ($has_children && $depth === 0) {
            $toggle_class = ($is_current || $is_current_parent) ? 'text-white' : 'text-gray-700 dark:text-gray-300';
            $item_output .= '<button type="button" class="desktop-submenu-toggle p-4 ' . $toggle_class . ' bg-secondary hover:text-white transition-colors duration-200" aria-expanded="false" aria-label="' . esc_attr__('Ouvrir le sous-menu', 'parisii-optique') . '">';
            $item_output .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down transition-transform duration-200"><path d="m6 9 6 6 6-6"/></svg>';
            $item_output .= '</button>';
        } elseif ($has_children && $depth > 0) {
            // Bouton chevron à droite pour les sous-sous-menus - même padding que les liens
            $toggle_class = ($is_current || $is_current_parent) ? 'text-white' : 'text-gray-700 dark:text-gray-300';
            $item_output .= '<button type="button" class="desktop-submenu-toggle-nested px-4 py-3.75 ' . $toggle_class . ' bg-secondary group-hover:text-white transition-colors duration-200" aria-expanded="false" aria-label="' . esc_attr__('Ouvrir le sous-menu', 'parisii-optique') . '">';
            $item_output .= '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right transition-transform duration-200"><path d="m9 18 6-6-6-6"/></svg>';
            $item_output .= '</button>';
        }
        
        $item_output .= isset($args->after) ? $args->after : '';
        
        // Fermer le wrapper si a des enfants
        if ($has_children) {
            $item_output .= '</div>';
        }
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

/**
 * Custom Walker for Mobile Navigation
 */
class Parisii_Optique_Walker_Nav_Menu_Mobile extends Walker_Nav_Menu {
    
    private $current_parent_id = null;
    
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        // Le sous-menu est ouvert si le parent est actif
        $hidden_class = ($this->current_parent_id !== null) ? '' : 'hidden';
        $output .= "\n$indent<ul class=\"submenu-mobile $hidden_class flex flex-col gap-2\">\n";
    }
    
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
        // Reset après la fermeture du sous-menu
        $this->current_parent_id = null;
    }
    
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        // Ajouter border seulement au niveau 0
        if ($depth === 0) {
            $classes[] = 'border-b border-b-gray-200 dark:border-b-gray-700 menu-item-' . $item->ID;
        } else {
            $classes[] = 'menu-item-' . $item->ID;
        }
        
        // Détecter si c'est un parent actif
        $is_current_parent = in_array('current-menu-parent', $classes) || in_array('current-menu-ancestor', $classes);
        $is_current = in_array('current-menu-item', $classes);
        
        // Définir l'ID du parent actif pour ouvrir son sous-menu
        if ($is_current_parent && $depth === 0) {
            $this->current_parent_id = $item->ID;
        }
        
        // Enlever les classes d'icônes
        $classes = array_filter($classes, function($class) {
            return strpos($class, 'dashicons') === false && strpos($class, 'menu-item-icon') === false;
        });
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        // Vérifier si l'item a des enfants
        $has_children = in_array('menu-item-has-children', $classes);
        
        // Classes actives
        $active_wrapper_class = ($is_current || $is_current_parent) ? 'bg-main text-white' : '';
        
        // Wrapper pour le contenu de l'item
        if ($has_children) {
            if ($depth === 0) {
                $output .= '<div class="flex items-center justify-between pl-4 bg-main ' . $active_wrapper_class . '">';
            } else {
                // Même style pour tous les sous-niveaux - hover uniquement, pas de padding (sera sur le lien)
                $output .= '<div class="flex items-center justify-between bg-main ' . $active_wrapper_class . '">';
            }
        }
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        // Classes différentes selon la profondeur et si a des enfants
        $active_link_class = ($is_current || $is_current_parent) ? 'bg-main text-white' : 'text-gray-700 dark:text-gray-300';
        
        if ($depth === 0) {
            if ($has_children) {
                $link_classes = 'nav-link flex-1 text-base font-medium ' . $active_link_class . ' hover:text-white hover:bg-main focus:bg-secondary';
            } else {
                $link_classes = 'nav-link block p-4 text-base font-medium ' . $active_link_class . ' hover:text-white hover:bg-main focus:bg-secondary';
            }
        } else {
            // Même style pour tous les sous-niveaux (enfants et enfants d'enfants)
            // Même padding pour tous : px-4 py-3
            if ($has_children) {
                $link_classes = 'nav-link flex-1 p-4 text-sm font-medium ' . $active_link_class . ' hover:text-white';
            } else {
                $link_classes = 'nav-link block p-4 text-sm font-medium ' . $active_link_class . ' hover:bg-main hover:text-white focus:bg-secondary';
            }
        }
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . ' class="' . $link_classes . '">';
        
        // Titre du menu
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        
        $item_output .= '</a>';
        
        // Ajouter bouton toggle si l'item a des enfants
        if ($has_children) {
            $aria_expanded = ($is_current_parent && $depth === 0) ? 'true' : 'false';
            $toggle_class = ($is_current || $is_current_parent) ? 'text-white' : 'text-gray-700 dark:text-gray-300';
            
            if ($depth === 0) {
                $item_output .= '<button type="button" class="submenu-toggle p-4 ' . $toggle_class . ' hover:text-white bg-secondary bg-secondary transition-all duration-200" aria-expanded="' . $aria_expanded . '" aria-label="' . esc_attr__('Ouvrir le sous-menu', 'parisii-optique') . '">';
            } else {
                // Même style pour tous les sous-niveaux
                $item_output .= '<button type="button" class="submenu-toggle p-4 ' . $toggle_class . ' hover:text-white bg-secondary bg-secondary transition-all duration-200" aria-expanded="false" aria-label="' . esc_attr__('Ouvrir le sous-menu', 'parisii-optique') . '">';
            }
            
            $item_output .= '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down transition-transform duration-200"><path d="m6 9 6 6 6-6"/></svg>';
            $item_output .= '</button>';
        }
        
        $item_output .= isset($args->after) ? $args->after : '';
        
        // Fermer le wrapper si a des enfants
        if ($has_children) {
            $item_output .= '</div>';
        }
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

/**
 * Custom Walker for Footer Navigation
 */
class Parisii_Optique_Walker_Nav_Menu_Footer extends Walker_Nav_Menu {
    
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }
    
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes . ' class="text-gray-400 dark:text-gray-400 hover:text-white">';
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

/**
 * Add customizer settings for footer
 */
function parisii_optique_customize_register_footer($wp_customize) {
    // Footer Section
    $wp_customize->add_section('footer_section', [
        'title' => __('Footer', 'parisii-optique'),
        'priority' => 40,
    ]);
    
    // Footer Description
    $wp_customize->add_setting('footer_description', [
        'default' => 'Votre partenaire de confiance pour tous vos besoins en optique.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    
    $wp_customize->add_control('footer_description', [
        'label' => __('Description du footer', 'parisii-optique'),
        'section' => 'footer_section',
        'type' => 'textarea',
    ]);
    
    // Contact Information
    $wp_customize->add_setting('phone_number', [
        'default' => '+33 1 23 45 67 89',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('phone_number', [
        'label' => __('Numéro de téléphone', 'parisii-optique'),
        'section' => 'footer_section',
        'type' => 'text',
    ]);
    
    $wp_customize->add_setting('email_address', [
        'default' => 'contact@parisii-optique.fr',
        'sanitize_callback' => 'sanitize_email',
    ]);
    
    $wp_customize->add_control('email_address', [
        'label' => __('Adresse email', 'parisii-optique'),
        'section' => 'footer_section',
        'type' => 'email',
    ]);
    
    // Address
    $wp_customize->add_setting('address_street', [
        'default' => '100 route de Seine',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('address_street', [
        'label' => __('Adresse (rue)', 'parisii-optique'),
        'section' => 'footer_section',
        'type' => 'text',
    ]);
    
    $wp_customize->add_setting('address_city', [
        'default' => '95249 Cormeilles-en-Parisis',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('address_city', [
        'label' => __('Adresse (ville)', 'parisii-optique'),
        'section' => 'footer_section',
        'type' => 'text',
    ]);
    
    // Hours
    $wp_customize->add_setting('hours_weekdays', [
        'default' => '9h30 - 19h00',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('hours_weekdays', [
        'label' => __('Horaires Lun - Ven', 'parisii-optique'),
        'section' => 'footer_section',
        'type' => 'text',
    ]);
    
    $wp_customize->add_setting('hours_saturday', [
        'default' => '10h00 - 19h30',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('hours_saturday', [
        'label' => __('Horaires Samedi', 'parisii-optique'),
        'section' => 'footer_section',
        'type' => 'text',
    ]);
    
    $wp_customize->add_setting('hours_sunday', [
        'default' => 'Fermé',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('hours_sunday', [
        'label' => __('Horaires Dimanche', 'parisii-optique'),
        'section' => 'footer_section',
        'type' => 'text',
    ]);
}
add_action('customize_register', 'parisii_optique_customize_register_footer');
