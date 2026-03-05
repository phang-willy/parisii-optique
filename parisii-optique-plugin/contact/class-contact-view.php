<?php
/**
 * Admin view single contact message
 *
 * @package Parisii_Optique_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Contact_View {

    public function render($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'po_contact';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);
        if (!$row) {
            echo '<div class="notice notice-error"><p>' . esc_html__('Message introuvable.', 'parisii-optique-plugin') . '</p></div>';
            return;
        }
        $user_id = get_current_user_id();
        $has_read_column = $wpdb->get_var($wpdb->prepare("SHOW COLUMNS FROM `$table` LIKE %s", 'read_flag'));
        $already_read = !empty($row['read_flag']);
        if ($has_read_column && !$already_read) {
            $wpdb->update(
                $table,
                array(
                    'read_flag' => 1,
                    'read_by'  => $user_id,
                    'read_at'  => current_time('mysql'),
                ),
                array('id' => $id),
                array('%d', '%d', '%s'),
                array('%d')
            );
            $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id), ARRAY_A);
        }

        $list_url = admin_url('admin.php?page=parisii-optique-contact');
        $read_by_name = '';
        if (!empty($row['read_by'])) {
            $read_by_user = get_userdata((int) $row['read_by']);
            $read_by_name = $read_by_user ? $read_by_user->display_name : '';
        }
        ?>
        <p><a href="<?php echo esc_url($list_url); ?>" class="button button-secondary parisii-contact-back"><?php esc_html_e('Retour à la liste', 'parisii-optique-plugin'); ?></a></p>
        <div class="parisii-contact-view-card">
            <table class="form-table">
                <tr>
                    <th><?php esc_html_e('Date', 'parisii-optique-plugin'); ?></th>
                    <td><?php echo esc_html(mysql2date(get_option('date_format') . ' à ' . get_option('time_format'), $row['date_created'])); ?></td>
                </tr>
                <?php if (isset($row['read_flag']) && (int) $row['read_flag'] === 1) : ?>
                <tr>
                    <th><?php esc_html_e('Lu', 'parisii-optique-plugin'); ?></th>
                    <td>
                        <?php
                        echo esc_html__('Oui', 'parisii-optique-plugin');
                        if (!empty($row['read_at'])) {
                            echo ' — ' . esc_html(mysql2date(get_option('date_format') . ' à ' . get_option('time_format'), $row['read_at']));
                        }
                        if ($read_by_name !== '') {
                            echo ' ' . sprintf(esc_html__('par %s', 'parisii-optique-plugin'), esc_html($read_by_name));
                        }
                        ?>
                    </td>
                </tr>
                <?php else : ?>
                <tr>
                    <th><?php esc_html_e('Lu', 'parisii-optique-plugin'); ?></th>
                    <td><?php esc_html_e('Non', 'parisii-optique-plugin'); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th><?php esc_html_e('Nom', 'parisii-optique-plugin'); ?></th>
                    <td><?php echo esc_html($row['nom']); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Prénom', 'parisii-optique-plugin'); ?></th>
                    <td><?php echo esc_html($row['prenom']); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Email', 'parisii-optique-plugin'); ?></th>
                    <td><a href="mailto:<?php echo esc_attr($row['email']); ?>"><?php echo esc_html($row['email']); ?></a></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Téléphone', 'parisii-optique-plugin'); ?></th>
                    <td><?php echo esc_html($row['tel']); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Sujet', 'parisii-optique-plugin'); ?></th>
                    <td><?php echo esc_html($row['sujet']); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e('Message', 'parisii-optique-plugin'); ?></th>
                    <td><?php echo nl2br(esc_html($row['message'])); ?></td>
                </tr>
            </table>
        </div>
        <?php
    }
}
