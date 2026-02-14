<?php
/**
 * Brand List class
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Brand_List {
    
    /**
     * Handle actions
     */
    private function handle_actions() {
        // Handle bulk delete
        if (isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {
            check_admin_referer('parisii_optique_bulk_action');
            
            if (!empty($_POST['brands'])) {
                foreach ($_POST['brands'] as $brand_id) {
                    Parisii_Optique_Brand::delete(absint($brand_id));
                }
                
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Marques supprimées avec succès.', 'parisii-optique-plugin') . '</p></div>';
            }
        }
        
        // Handle single delete
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            check_admin_referer('parisii_optique_delete_' . absint($_GET['id']));
            
            $brand_id = absint($_GET['id']);
            Parisii_Optique_Brand::delete($brand_id);
            
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Marque supprimée avec succès.', 'parisii-optique-plugin') . '</p></div>';
        }
    }
    
    /**
     * Render the list page
     */
    public function render() {
        $this->handle_actions();
        
        // Get search and sorting parameters
        $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
        $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'name';
        $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'ASC';
        
        // Toggle order for next click
        $next_order = $order === 'ASC' ? 'DESC' : 'ASC';
        
        // Get brands
        $brands = Parisii_Optique_Brand::get_all(array(
            'search' => $search,
            'orderby' => $orderby,
            'order' => $order,
        ));
        
        ?>
        <div class="tablenav top">
            <div class="alignleft actions">
                <a href="<?php echo add_query_arg(array('tab' => 'add'), admin_url('admin.php?page=parisii-optique-brands')); ?>" class="button button-primary">
                    <?php _e('Ajouter une marque', 'parisii-optique-plugin'); ?>
                </a>
            </div>
            
            <form method="get" class="search-form">
                <input type="hidden" name="page" value="parisii-optique-brands">
                <input type="hidden" name="tab" value="list">
                <p class="search-box">
                    <label class="screen-reader-text" for="brand-search-input"><?php _e('Rechercher des marques:', 'parisii-optique-plugin'); ?></label>
                    <input type="search" id="brand-search-input" name="s" value="<?php echo esc_attr($search); ?>" placeholder="<?php esc_attr_e('Rechercher...', 'parisii-optique-plugin'); ?>">
                    <input type="submit" id="search-submit" class="button" value="<?php esc_attr_e('Rechercher', 'parisii-optique-plugin'); ?>">
                </p>
            </form>
        </div>
        
        <form method="post">
            <?php wp_nonce_field('parisii_optique_bulk_action'); ?>
            <input type="hidden" name="action" value="bulk_delete">
            
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <select name="action2">
                        <option value=""><?php _e('Actions groupées', 'parisii-optique-plugin'); ?></option>
                        <option value="bulk_delete"><?php _e('Supprimer', 'parisii-optique-plugin'); ?></option>
                    </select>
                    <input type="submit" class="button action" value="<?php esc_attr_e('Appliquer', 'parisii-optique-plugin'); ?>" onclick="return confirm('<?php esc_attr_e('Êtes-vous sûr de vouloir supprimer les marques sélectionnées ?', 'parisii-optique-plugin'); ?>');">
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" id="cb-select-all">
                        </td>
                        <th scope="col" class="manage-column column-primary sortable <?php echo $orderby === 'name' ? strtolower($order) : ''; ?>">
                            <a href="<?php echo add_query_arg(array('orderby' => 'name', 'order' => $next_order)); ?>">
                                <span><?php _e('Nom', 'parisii-optique-plugin'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th scope="col" class="manage-column">
                            <?php _e('Logo', 'parisii-optique-plugin'); ?>
                        </th>
                        <th scope="col" class="manage-column sortable <?php echo $orderby === 'visible' ? strtolower($order) : ''; ?>">
                            <a href="<?php echo add_query_arg(array('orderby' => 'visible', 'order' => $next_order)); ?>">
                                <span><?php _e('Visible', 'parisii-optique-plugin'); ?></span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($brands)) : ?>
                        <tr>
                            <td colspan="4" class="no-items"><?php _e('Aucune marque trouvée.', 'parisii-optique-plugin'); ?></td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($brands as $brand) : ?>
                            <?php
                            $edit_url = add_query_arg(array('tab' => 'edit', 'id' => $brand->id), admin_url('admin.php?page=parisii-optique-brands'));
                            $delete_url = wp_nonce_url(add_query_arg(array('tab' => 'delete', 'id' => $brand->id)), 'parisii_optique_delete_' . $brand->id);
                            ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="brands[]" value="<?php echo esc_attr($brand->id); ?>">
                                </th>
                                <td class="column-primary" data-colname="<?php esc_attr_e('Nom', 'parisii-optique-plugin'); ?>">
                                    <strong>
                                        <a href="<?php echo esc_url($edit_url); ?>"><?php echo esc_html($brand->name); ?></a>
                                    </strong>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="<?php echo esc_url($edit_url); ?>"><?php _e('Modifier', 'parisii-optique-plugin'); ?></a> |
                                        </span>
                                        <span class="delete">
                                            <a href="<?php echo esc_url($delete_url); ?>" onclick="return confirm('<?php esc_attr_e('Êtes-vous sûr de vouloir supprimer cette marque ?', 'parisii-optique-plugin'); ?>');"><?php _e('Supprimer', 'parisii-optique-plugin'); ?></a>
                                        </span>
                                    </div>
                                </td>
                                <td data-colname="<?php esc_attr_e('Logo', 'parisii-optique-plugin'); ?>">
                                    <?php if (!empty($brand->logo)) : ?>
                                        <img src="<?php echo esc_url($brand->logo); ?>" alt="<?php echo esc_attr($brand->name); ?> " style="width: 55px; height: 35px; object-fit: contain;">
                                    <?php else : ?>
                                        <span class="dashicons dashicons-format-image" style="font-size: 50px; color: #ddd;"></span>
                                    <?php endif; ?>
                                </td>
                                <td data-colname="<?php esc_attr_e('Visible', 'parisii-optique-plugin'); ?>">
                                    <?php if ($brand->visible) : ?>
                                        <span class="dashicons dashicons-yes-alt" style="color: #46b450;"></span>
                                    <?php else : ?>
                                        <span class="dashicons dashicons-dismiss" style="color: #dc3232;"></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
        
        <script>
        jQuery(document).ready(function($) {
            $('#cb-select-all').on('click', function() {
                $('input[name="brands[]"]').prop('checked', this.checked);
            });
        });
        </script>
        <?php
    }
}

