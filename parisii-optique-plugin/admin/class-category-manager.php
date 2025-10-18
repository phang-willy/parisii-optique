<?php
/**
 * Category Manager class
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Category_Manager {
    
    /**
     * Handle actions
     */
    private function handle_actions() {
        // Handle create
        if (isset($_POST['action']) && $_POST['action'] === 'create') {
            check_admin_referer('parisii_optique_create_category');
            
            $category_id = Parisii_Optique_Brand_Category::create(array(
                'name' => sanitize_text_field($_POST['category_name']),
            ));
            
            if ($category_id) {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Catégorie créée avec succès.', 'parisii-optique-plugin') . '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('Erreur lors de la création de la catégorie.', 'parisii-optique-plugin') . '</p></div>';
            }
        }
        
        // Handle update
        if (isset($_POST['action']) && $_POST['action'] === 'update') {
            check_admin_referer('parisii_optique_update_category_' . absint($_POST['category_id']));
            
            $result = Parisii_Optique_Brand_Category::update(
                absint($_POST['category_id']),
                array('name' => sanitize_text_field($_POST['category_name']))
            );
            
            if ($result !== false) {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Catégorie mise à jour avec succès.', 'parisii-optique-plugin') . '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('Erreur lors de la mise à jour de la catégorie.', 'parisii-optique-plugin') . '</p></div>';
            }
        }
        
        // Handle delete
        if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
            check_admin_referer('parisii_optique_delete_category_' . absint($_GET['id']));
            
            $category_id = absint($_GET['id']);
            $result = Parisii_Optique_Brand_Category::delete($category_id);
            
            if ($result !== false) {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Catégorie supprimée avec succès.', 'parisii-optique-plugin') . '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('Erreur lors de la suppression de la catégorie.', 'parisii-optique-plugin') . '</p></div>';
            }
        }
    }
    
    /**
     * Render the categories page
     */
    public function render() {
        $this->handle_actions();
        
        // Get edit mode
        $edit_id = isset($_GET['edit']) ? absint($_GET['edit']) : 0;
        $edit_category = null;
        
        if ($edit_id) {
            $edit_category = Parisii_Optique_Brand_Category::get_by_id($edit_id);
        }
        
        // Get all categories
        $categories = Parisii_Optique_Brand_Category::get_all();
        
        ?>
        <div class="wrap">
            <h1><?php _e('Catégories de marques', 'parisii-optique-plugin'); ?></h1>
            
            <div class="parisii-optique-categories-grid" style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-top: 20px;">
                
                <!-- Form: Create/Edit Category -->
                <div class="parisii-optique-category-form-wrapper">
                    <div class="card" style="padding: 20px;">
                        <h2><?php echo $edit_category ? __('Modifier la catégorie', 'parisii-optique-plugin') : __('Nouvelle catégorie', 'parisii-optique-plugin'); ?></h2>
                        
                        <form method="post">
                            <?php if ($edit_category) : ?>
                                <?php wp_nonce_field('parisii_optique_update_category_' . $edit_category->id); ?>
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="category_id" value="<?php echo esc_attr($edit_category->id); ?>">
                            <?php else : ?>
                                <?php wp_nonce_field('parisii_optique_create_category'); ?>
                                <input type="hidden" name="action" value="create">
                            <?php endif; ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="category_name"><?php _e('Nom', 'parisii-optique-plugin'); ?> <span class="required">*</span></label>
                                    </th>
                                    <td>
                                        <input type="text" id="category_name" name="category_name" value="<?php echo $edit_category ? esc_attr($edit_category->name) : ''; ?>" class="regular-text" required>
                                    </td>
                                </tr>
                            </table>
                            
                            <p class="submit">
                                <input type="submit" class="button button-primary" value="<?php echo $edit_category ? esc_attr__('Mettre à jour', 'parisii-optique-plugin') : esc_attr__('Créer', 'parisii-optique-plugin'); ?>">
                                <?php if ($edit_category) : ?>
                                    <a href="<?php echo add_query_arg(array('tab' => 'categories'), admin_url('admin.php?page=parisii-optique-brands')); ?>" class="button"><?php _e('Annuler', 'parisii-optique-plugin'); ?></a>
                                <?php endif; ?>
                            </p>
                        </form>
                    </div>
                </div>
                
                <!-- List: Categories -->
                <div class="parisii-optique-categories-list">
                    <?php if (empty($categories)) : ?>
                        <div class="card" style="padding: 20px;">
                            <p><?php _e('Aucune catégorie trouvée. Créez-en une pour commencer.', 'parisii-optique-plugin'); ?></p>
                        </div>
                    <?php else : ?>
                        <div class="parisii-optique-category-cards" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px;">
                            <?php foreach ($categories as $category) : ?>
                                <?php
                                $brand_count = Parisii_Optique_Brand_Category::count_brands($category->id);
                                $edit_url = add_query_arg(array('tab' => 'categories', 'edit' => $category->id), admin_url('admin.php?page=parisii-optique-brands'));
                                $delete_url = wp_nonce_url(add_query_arg(array('tab' => 'categories', 'action' => 'delete', 'id' => $category->id)), 'parisii_optique_delete_category_' . $category->id);
                                ?>
                                <div class="card parisii-optique-category-card" style="padding: 20px; position: relative;">
                                    <h3 style="margin-top: 0;"><?php echo esc_html($category->name); ?></h3>
                                    <p class="description" style="margin-bottom: 15px;">
                                        <?php printf(_n('%d marque', '%d marques', $brand_count, 'parisii-optique-plugin'), $brand_count); ?>
                                    </p>
                                    <div class="row-actions">
                                        <a href="<?php echo esc_url($edit_url); ?>" class="button button-small"><?php _e('Modifier', 'parisii-optique-plugin'); ?></a>
                                        <a href="<?php echo esc_url($delete_url); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php esc_attr_e('Êtes-vous sûr de vouloir supprimer cette catégorie ?', 'parisii-optique-plugin'); ?>');" style="color: #d63638;"><?php _e('Supprimer', 'parisii-optique-plugin'); ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
        <?php
    }
}

