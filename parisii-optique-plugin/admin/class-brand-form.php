<?php
/**
 * Brand Form class
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Brand_Form {
    
    /**
     * Handle form submission
     */
    private function handle_submit($brand_id = 0) {
        if (!isset($_POST['parisii_optique_brand_nonce'])) {
            return;
        }
        
        check_admin_referer('parisii_optique_save_brand', 'parisii_optique_brand_nonce');
        
        $data = array(
            'name' => sanitize_text_field($_POST['brand_name']),
            'logo' => esc_url_raw($_POST['brand_logo']),
            'visible' => isset($_POST['brand_visible']) ? 1 : 0,
            'kids' => isset($_POST['brand_kids']) ? 1 : 0,
        );
        
        if ($brand_id) {
            // Update existing brand
            $result = Parisii_Optique_Brand::update($brand_id, $data);
            if ($result !== false) {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Marque mise à jour avec succès.', 'parisii-optique-plugin') . '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('Erreur lors de la mise à jour de la marque.', 'parisii-optique-plugin') . '</p></div>';
            }
        } else {
            // Create new brand
            $new_id = Parisii_Optique_Brand::create($data);
            if ($new_id) {
                echo '<div class="notice notice-success is-dismissible"><p>' . __('Marque créée avec succès.', 'parisii-optique-plugin') . ' <a href="' . add_query_arg(array('tab' => 'edit', 'id' => $new_id), admin_url('admin.php?page=parisii-optique-brands')) . '">' . __('Modifier', 'parisii-optique-plugin') . '</a></p></div>';
                $brand_id = $new_id;
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('Erreur lors de la création de la marque.', 'parisii-optique-plugin') . '</p></div>';
            }
        }
        
        return $brand_id;
    }
    
    /**
     * Delete brand
     */
    public function delete($brand_id) {
        check_admin_referer('parisii_optique_delete_' . $brand_id);
        
        $brand = Parisii_Optique_Brand::get_by_id($brand_id);
        
        if (!$brand) {
            echo '<div class="notice notice-error"><p>' . __('Marque introuvable.', 'parisii-optique-plugin') . '</p></div>';
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php _e('Supprimer une marque', 'parisii-optique-plugin'); ?></h1>
            
            <div class="notice notice-warning">
                <p><?php printf(__('Êtes-vous sûr de vouloir supprimer la marque "%s" ?', 'parisii-optique-plugin'), esc_html($brand->name)); ?></p>
                <p><?php _e('Cette action est irréversible.', 'parisii-optique-plugin'); ?></p>
            </div>
            
            <form method="post">
                <?php wp_nonce_field('parisii_optique_confirm_delete', 'parisii_optique_delete_nonce'); ?>
                <p>
                    <input type="submit" name="confirm_delete" class="button button-primary" value="<?php esc_attr_e('Confirmer la suppression', 'parisii-optique-plugin'); ?>">
                    <a href="<?php echo add_query_arg(array('tab' => 'list'), admin_url('admin.php?page=parisii-optique-brands')); ?>" class="button"><?php _e('Annuler', 'parisii-optique-plugin'); ?></a>
                </p>
            </form>
        </div>
        <?php
        
        if (isset($_POST['confirm_delete'])) {
            check_admin_referer('parisii_optique_confirm_delete', 'parisii_optique_delete_nonce');
            
            Parisii_Optique_Brand::delete($brand_id);
            
            wp_redirect(add_query_arg(array('page' => 'parisii-optique-brands', 'tab' => 'list', 'deleted' => '1'), admin_url('admin.php')));
            exit;
        }
    }
    
    /**
     * Render the form
     */
    public function render($brand_id = 0) {
        // Handle form submission
        if (isset($_POST['parisii_optique_brand_nonce'])) {
            $brand_id = $this->handle_submit($brand_id);
        }
        
        // Get brand data if editing
        $brand = null;
        
        if ($brand_id) {
            $brand = Parisii_Optique_Brand::get_by_id($brand_id);
            if (!$brand) {
                echo '<div class="notice notice-error"><p>' . __('Marque introuvable.', 'parisii-optique-plugin') . '</p></div>';
                return;
            }
        }
        
        $name = $brand ? $brand->name : '';
        $logo = $brand ? $brand->logo : '';
        $visible = $brand ? $brand->visible : 1;
        $kids = $brand ? (isset($brand->kids) ? $brand->kids : 0) : 0;
        
        ?>
        <div class="wrap">
            <h1><?php echo $brand_id ? __('Modifier une marque', 'parisii-optique-plugin') : __('Nouvelle marque', 'parisii-optique-plugin'); ?></h1>
            
            <form method="post" class="parisii-optique-brand-form">
                <?php wp_nonce_field('parisii_optique_save_brand', 'parisii_optique_brand_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="brand_name"><?php _e('Nom de la marque', 'parisii-optique-plugin'); ?> <span class="required">*</span></label>
                        </th>
                        <td>
                            <input type="text" id="brand_name" name="brand_name" value="<?php echo esc_attr($name); ?>" class="regular-text" required>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="brand_logo"><?php _e('Logo (URL)', 'parisii-optique-plugin'); ?></label>
                        </th>
                        <td>
                            <input type="url" id="brand_logo" name="brand_logo" value="<?php echo esc_url($logo); ?>" class="regular-text">
                            <button type="button" class="button parisii-optique-upload-image"><?php _e('Télécharger une image', 'parisii-optique-plugin'); ?></button>
                            <p class="description"><?php _e('URL du logo de la marque', 'parisii-optique-plugin'); ?></p>
                            <?php if ($logo) : ?>
                                <p class="parisii-optique-logo-preview">
                                    <img src="<?php echo esc_url($logo); ?>" alt="" style="max-width: 150px; max-height: 150px; margin-top: 10px;">
                                </p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="brand_visible"><?php _e('Visible sur le site', 'parisii-optique-plugin'); ?></label>
                        </th>
                        <td>
                            <label for="brand_visible">
                                <input type="checkbox" id="brand_visible" name="brand_visible" value="1" <?php checked($visible, 1); ?>>
                                <?php _e('Afficher cette marque sur le site', 'parisii-optique-plugin'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="brand_kids"><?php _e('Marque enfant', 'parisii-optique-plugin'); ?></label>
                        </th>
                        <td>
                            <label for="brand_kids">
                                <input type="checkbox" id="brand_kids" name="brand_kids" value="1" <?php checked($kids, 1); ?>>
                                <?php _e('Afficher cette marque dans la selection enfant', 'parisii-optique-plugin'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $brand_id ? esc_attr__('Mettre à jour', 'parisii-optique-plugin') : esc_attr__('Créer', 'parisii-optique-plugin'); ?>">
                    <a href="<?php echo admin_url('admin.php?page=parisii-optique-brands'); ?>" class="button"><?php _e('Annuler', 'parisii-optique-plugin'); ?></a>
                </p>
            </form>
        </div>
        <?php
    }
}

