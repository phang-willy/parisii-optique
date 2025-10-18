<?php
/**
 * Menu Admin Simple Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add product categories to menu admin
 */
function parisii_optique_add_product_categories_to_menu_admin_simple() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Add product categories to menu admin
    add_meta_box(
        'parisii-product-categories',
        __('Catégories de Produits', 'parisii-optique'),
        'parisii_optique_product_categories_meta_box_simple',
        'nav-menus',
        'side',
        'default'
    );
}
add_action('admin_init', 'parisii_optique_add_product_categories_to_menu_admin_simple');

/**
 * Product categories meta box
 */
function parisii_optique_product_categories_meta_box_simple() {
    // Get recent categories (most recently modified)
    $recent_categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'number' => 15,
        'orderby' => 'term_id',
        'order' => 'DESC',
    ]);
    
    // Get all categories
    $all_categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);
    
    ?>
    <div id="parisii-product-categories" class="posttypediv">
        <ul id="parisii-product-categories-tabs" class="category-tabs">
            <li class="tabs"><a href="#tabs-panel-parisii-product-categories-pop" class="nav-tab-link" data-type="tabs-panel-parisii-product-categories-pop"><?php _e('Les plus récentes', 'parisii-optique'); ?></a></li>
            <li class="hide-if-no-js"><a href="#tabs-panel-parisii-product-categories-all" class="nav-tab-link" data-type="tabs-panel-parisii-product-categories-all"><?php _e('Tout voir', 'parisii-optique'); ?></a></li>
            <li class="hide-if-no-js"><a href="#tabs-panel-parisii-product-categories-search" class="nav-tab-link" data-type="tabs-panel-parisii-product-categories-search"><?php _e('Rechercher', 'parisii-optique'); ?></a></li>
        </ul>

        <div id="tabs-panel-parisii-product-categories-pop" class="tabs-panel tabs-panel-active">
            <ul id="parisii-product-categories-checklist-pop" class="categorychecklist form-no-clear">
                <?php
                if (!is_wp_error($recent_categories) && !empty($recent_categories)) {
                    foreach ($recent_categories as $category) {
                        parisii_optique_render_category_item($category);
                    }
                } else {
                    echo '<li>' . __('Aucune catégorie récente', 'parisii-optique') . '</li>';
                }
                ?>
            </ul>
        </div>

        <div id="tabs-panel-parisii-product-categories-all" class="tabs-panel tabs-panel-view-all">
            <ul id="parisii-product-categories-checklist-all" class="categorychecklist form-no-clear">
                <?php
                if (is_wp_error($all_categories) || empty($all_categories)) {
                    echo '<li>' . __('Aucune catégorie de produit trouvée', 'parisii-optique') . '</li>';
                } else {
                    foreach ($all_categories as $category) {
                        parisii_optique_render_category_item($category);
                    }
                }
                ?>
            </ul>
        </div>

        <div id="tabs-panel-parisii-product-categories-search" class="tabs-panel">
            <p class="quick-search-wrap">
                <label for="quick-search-parisii-product-categories" class="screen-reader-text"><?php _e('Rechercher des catégories', 'parisii-optique'); ?></label>
                <input type="search" class="quick-search" name="quick-search-parisii-product-categories" id="quick-search-parisii-product-categories" placeholder="<?php esc_attr_e('Rechercher...', 'parisii-optique'); ?>" />
                <span class="spinner"></span>
            </p>
            <ul id="parisii-product-categories-checklist-search" class="categorychecklist form-no-clear">
                <li class="parisii-search-placeholder"><?php _e('Saisissez votre recherche ci-dessus', 'parisii-optique'); ?></li>
            </ul>
        </div>

        <p class="button-controls">
            <span class="list-controls">
                <a href="<?php echo esc_url(admin_url('nav-menus.php?parisii-product-categories-tab=all#parisii-product-categories-all')); ?>" class="select-all"><?php _e('Tout sélectionner', 'parisii-optique'); ?></a>
            </span>
            <span class="add-to-menu">
                <input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Ajouter au menu', 'parisii-optique'); ?>" name="add-parisii-product-categories-menu-item" id="submit-parisii-product-categories" />
                <span class="spinner"></span>
            </span>
        </p>
    </div>
    <?php
}

/**
 * Render category item for meta box
 */
function parisii_optique_render_category_item($category) {
    ?>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $category->term_id; ?>][menu-item-object-id]" value="<?php echo $category->term_id; ?>" />
                            <?php echo esc_html($category->name); ?>
                            <?php if ($category->count > 0) : ?>
                                <span class="menu-item-count">(<?php echo $category->count; ?>)</span>
                            <?php endif; ?>
                        </label>
                        
                        <input type="hidden" class="menu-item-db-id" name="menu-item[<?php echo $category->term_id; ?>][menu-item-db-id]" value="<?php echo $category->term_id; ?>" />
                        <input type="hidden" class="menu-item-object" name="menu-item[<?php echo $category->term_id; ?>][menu-item-object]" value="product_cat" />
                        <input type="hidden" class="menu-item-parent-id" name="menu-item[<?php echo $category->term_id; ?>][menu-item-parent-id]" value="0" />
                        <input type="hidden" class="menu-item-type" name="menu-item[<?php echo $category->term_id; ?>][menu-item-type]" value="taxonomy" />
                        <input type="hidden" class="menu-item-title" name="menu-item[<?php echo $category->term_id; ?>][menu-item-title]" value="<?php echo esc_attr($category->name); ?>" />
                        <input type="hidden" class="menu-item-url" name="menu-item[<?php echo $category->term_id; ?>][menu-item-url]" value="<?php echo esc_attr(get_term_link($category)); ?>" />
                        <input type="hidden" class="menu-item-target" name="menu-item[<?php echo $category->term_id; ?>][menu-item-target]" value="" />
                        <input type="hidden" class="menu-item-attr-title" name="menu-item[<?php echo $category->term_id; ?>][menu-item-attr-title]" value="" />
                        <input type="hidden" class="menu-item-classes" name="menu-item[<?php echo $category->term_id; ?>][menu-item-classes]" value="menu-item-product-category" />
                        <input type="hidden" class="menu-item-xfn" name="menu-item[<?php echo $category->term_id; ?>][menu-item-xfn]" value="" />
                    </li>
    <?php
}

/**
 * Add product categories to menu admin CSS
 */
function parisii_optique_menu_admin_simple_css() {
    $screen = get_current_screen();
    if ($screen->id !== 'nav-menus') {
        return;
    }
    ?>
    <style>
    /* Tabs Navigation - Style WordPress natif */
    #parisii-product-categories .category-tabs {
        overflow: hidden;
        padding-top: 0;
        margin: 0;
    }
    
    #parisii-product-categories .category-tabs li {
        display: inline;
        line-height: 1.35em;
        padding: 0;
        margin: 0;
    }
    
    #parisii-product-categories .category-tabs a,
    #parisii-product-categories .category-tabs a:hover {
        border: none;
        text-decoration: none;
    }
    
    #parisii-product-categories .category-tabs li a {
        padding: 5px 10px 4px 10px;
        display: inline-block;
        color: #2c3338;
        text-decoration: none;
        border-width: 1px 1px 0;
        border-style: solid solid none;
        border-color: #c3c4c7 #c3c4c7 transparent;
        background-color: #f6f7f7;
        transition: none;
    }
    
    #parisii-product-categories .category-tabs li a:hover {
        color: #0a4b78;
        background-color: #fff;
    }
    
    #parisii-product-categories .category-tabs .tabs a {
        background-color: #f0f0f1;
        border-color: #c3c4c7 #c3c4c7 #f0f0f1;
    }
    
    #parisii-product-categories .category-tabs a.nav-tab-active {
        background-color: #f0f0f1;
        border-color: #c3c4c7 #c3c4c7 #f0f0f1;
        color: #000;
        padding-bottom: 5px;
    }
    
    /* Tabs Panels - Style WordPress natif */
    #parisii-product-categories .tabs-panel {
        display: none;
        min-height: 42px;
        max-height: 200px;
        overflow: auto;
        padding: 0;
        border: 1px solid #c3c4c7;
        border-width: 0 1px 1px;
        background: #f0f0f1;
    }
    
    #parisii-product-categories .tabs-panel-active {
        display: block;
    }
    
    #parisii-product-categories .tabs-panel-view-all {
        border-top: 1px solid #c3c4c7;
        margin-top: -1px;
    }
    
    /* Search box - Style WordPress natif */
    #parisii-product-categories .quick-search-wrap {
        padding: 8px;
        margin: 0;
        background: #fff;
        border-bottom: 1px solid #dfdfdf;
        box-sizing: border-box;
    }
    
    #parisii-product-categories .quick-search {
        width: 100%;
        margin: 0;
        padding: 3px 5px;
        line-height: 1.5;
    }
    
    #parisii-product-categories .quick-search-wrap .spinner {
        float: right;
        margin: 5px 5px 0 0;
    }
    
    /* Category list - Style WordPress natif */
    #parisii-product-categories .categorychecklist {
        margin: 0;
        padding: 12px;
        list-style: none;
        background: #f0f0f1;
    }
    
    #parisii-product-categories .categorychecklist li {
        margin: 0;
        padding: 4px 0;
        line-height: 22px;
        word-wrap: break-word;
        list-style: none;
    }
    
    #parisii-product-categories .categorychecklist label {
        display: inline-block;
        cursor: pointer;
        padding: 0;
        margin: 0;
        width: 100%;
    }
    
    #parisii-product-categories .categorychecklist label:hover {
        color: #0a4b78;
    }
    
    #parisii-product-categories .categorychecklist input[type="checkbox"] {
        margin: 0 5px 0 0;
        vertical-align: top;
    }
    
    #parisii-product-categories .dashicons {
        color: #2271b1;
        vertical-align: middle;
        width: 16px;
        height: 16px;
        font-size: 16px;
    }
    
    #parisii-product-categories .menu-item-count {
        color: #646970;
        font-size: 12px;
        margin-left: 3px;
    }
    
    #parisii-product-categories .parisii-search-placeholder {
        color: #646970;
        font-style: italic;
        padding: 8px 0;
    }
    
    /* Button controls - Style WordPress natif */
    #parisii-product-categories .button-controls {
        margin: 10px 0 0;
        padding: 0;
        clear: both;
        overflow: hidden;
    }
    
    #parisii-product-categories .list-controls {
        float: left;
        margin-top: 5px;
    }
    
    #parisii-product-categories .list-controls a {
        text-decoration: none;
    }
    
    #parisii-product-categories .add-to-menu {
        float: right;
    }
    
    #parisii-product-categories .add-to-menu .spinner {
        float: none;
        margin: 0 0 0 4px;
        vertical-align: middle;
    }
    
    /* Menu items styling */
    .menu-item-product-category {
        background-color: #f0f8ff;
        border-left: 3px solid #2271b1;
        margin: 2px 0;
    }
    
    .menu-item-product-category .item-title {
        font-weight: 600;
        color: #2271b1;
    }
    
    .menu-item-product-category .item-type {
        color: #2271b1;
        font-style: italic;
        font-size: 11px;
    }
    </style>
    <?php
}
add_action('admin_head', 'parisii_optique_menu_admin_simple_css');

/**
 * Add product categories to menu admin JavaScript
 */
function parisii_optique_menu_admin_simple_js() {
    $screen = get_current_screen();
    if ($screen->id !== 'nav-menus') {
        return;
    }
    ?>
    <script>
    jQuery(document).ready(function($) {
        var $metaBox = $('#parisii-product-categories');
        
        if (!$metaBox.length) {
            return;
        }

        var $tabsNav = $('#parisii-product-categories-tabs');
        var $searchInput = $('#quick-search-parisii-product-categories');
        var $searchResults = $('#parisii-product-categories-checklist-search');
        var $searchSpinner = $searchInput.siblings('.spinner');

        // Handle tab clicks
        $tabsNav.on('click', 'a', function(e) {
            e.preventDefault();
            var $this = $(this);
            var panelId = $this.attr('href');

            // Remove active class from all tabs and panels
            $tabsNav.find('a').removeClass('nav-tab-active');
            $metaBox.find('.tabs-panel').removeClass('tabs-panel-active');

            // Add active class to clicked tab and corresponding panel
            $this.addClass('nav-tab-active');
            $(panelId).addClass('tabs-panel-active');

            // Focus search input if search tab
            if (panelId === '#tabs-panel-parisii-product-categories-search') {
                $searchInput.focus();
            }
        });

        // Set first tab as active by default
        $tabsNav.find('a').first().addClass('nav-tab-active');

        // Handle search with debounce
        var searchTimer;
        $searchInput.on('input', function() {
            clearTimeout(searchTimer);
            var searchTerm = $(this).val().trim();

            if (searchTerm.length === 0) {
                $searchResults.html('<li class="parisii-search-placeholder"><?php _e('Saisissez votre recherche ci-dessus', 'parisii-optique'); ?></li>');
                return;
            }

            if (searchTerm.length < 2) {
                $searchResults.html('<li class="parisii-search-placeholder"><?php _e('Saisissez au moins 2 caractères', 'parisii-optique'); ?></li>');
                return;
            }

            $searchSpinner.addClass('is-active');

            searchTimer = setTimeout(function() {
                performSearch(searchTerm);
            }, 500);
        });

        function performSearch(searchTerm) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'parisii_search_product_categories',
                    search: searchTerm,
                    nonce: '<?php echo wp_create_nonce('parisii_search_categories'); ?>'
                },
                success: function(response) {
                    $searchSpinner.removeClass('is-active');
                    
                    if (response.success && response.data && response.data.length > 0) {
                        $searchResults.empty();
                        $.each(response.data, function(index, categoryHtml) {
                            $searchResults.append(categoryHtml);
                        });
                } else {
                        $searchResults.html('<li class="parisii-search-placeholder"><?php _e('Aucun résultat trouvé', 'parisii-optique'); ?></li>');
                    }
                },
                error: function() {
                    $searchSpinner.removeClass('is-active');
                    $searchResults.html('<li class="parisii-search-placeholder" style="color: #d63638;"><?php _e('Erreur lors de la recherche', 'parisii-optique'); ?></li>');
                }
            });
        }

        // Select all functionality
        $('#parisii-product-categories .select-all').on('click', function(e) {
            e.preventDefault();
            var $activePanel = $metaBox.find('.tabs-panel-active');
            $activePanel.find('input[type="checkbox"]').prop('checked', true);
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'parisii_optique_menu_admin_simple_js');

/**
 * AJAX handler for category search
 */
function parisii_optique_search_product_categories_ajax() {
    check_ajax_referer('parisii_search_categories', 'nonce');
    
    if (!current_user_can('edit_theme_options')) {
        wp_send_json_error(__('Permissions insuffisantes', 'parisii-optique'));
    }
    
    $search = sanitize_text_field($_POST['search']);
    
    if (strlen($search) < 2) {
        wp_send_json_error(__('Terme de recherche trop court', 'parisii-optique'));
    }
    
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'search' => $search,
        'number' => 20,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);
    
    if (is_wp_error($categories) || empty($categories)) {
        wp_send_json_success([]);
        return;
    }
    
    $results = [];
    foreach ($categories as $category) {
        ob_start();
        parisii_optique_render_category_item($category);
        $results[] = ob_get_clean();
    }
    
    wp_send_json_success($results);
}
add_action('wp_ajax_parisii_search_product_categories', 'parisii_optique_search_product_categories_ajax');
