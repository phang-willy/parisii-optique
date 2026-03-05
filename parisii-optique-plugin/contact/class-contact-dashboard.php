<?php
/**
 * Dashboard widget: statistiques des contacts
 *
 * @package Parisii_Optique_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Contact_Dashboard {

    public function __construct() {
        add_action('wp_dashboard_setup', array($this, 'add_widget'));
        add_action('admin_head-index.php', array($this, 'print_styles'));
    }

    public function print_styles() {
        echo '<style id="po-contact-dashboard">' . $this->get_widget_css() . '</style>';
    }

    private function get_widget_css() {
        return '
.po-contact-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 0 0 12px 0; }
.po-contact-stats .po-stat { background: #f6f7f7; border-radius: 4px; padding: 10px 12px; border-left: 4px solid #2271b1; }
.po-contact-stats .po-stat.po-stat-unread { border-left-color: #d63638; background: #fcf0f1; }
.po-contact-stats .po-stat .po-stat-label { font-size: 12px; color: #646970; line-height: 1.3; }
.po-contact-stats .po-stat .po-stat-value { font-size: 20px; font-weight: 600; color: #1d2327; line-height: 1.2; }
.po-contact-dashboard-footer { padding-top: 12px; margin-top: 0; border-top: 1px solid #c3c4c7; }
';
    }

    public function add_widget() {
        if (!current_user_can('manage_options')) {
            return;
        }
        wp_add_dashboard_widget(
            'parisii_optique_contact_stats',
            __('Statistiques Contact', 'parisii-optique-plugin'),
            array($this, 'render_widget'),
            null,
            null,
            'side'
        );
    }

    public function render_widget() {
        global $wpdb;
        $table = $wpdb->prefix . 'po_contact';

        $unread = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$table` WHERE (read_flag = 0 OR read_flag IS NULL)");
        $today = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$table` WHERE DATE(date_created) = CURDATE()");
        $week = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$table` WHERE YEARWEEK(date_created, 1) = YEARWEEK(CURDATE(), 1)");
        $month = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$table` WHERE MONTH(date_created) = MONTH(CURDATE()) AND YEAR(date_created) = YEAR(CURDATE())");
        $year = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$table` WHERE YEAR(date_created) = YEAR(CURDATE())");
        $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM `$table`");

        $contact_url = admin_url('admin.php?page=parisii-optique-contact&tab=list');
        ?>
        <div class="po-contact-stats">
            <div class="po-stat po-stat-unread">
                <div class="po-stat-label"><?php esc_html_e('Non lus', 'parisii-optique-plugin'); ?></div>
                <div class="po-stat-value"><?php echo esc_html((string) $unread); ?></div>
            </div>
            <div class="po-stat">
                <div class="po-stat-label"><?php esc_html_e('Aujourd\'hui', 'parisii-optique-plugin'); ?></div>
                <div class="po-stat-value"><?php echo esc_html((string) $today); ?></div>
            </div>
            <div class="po-stat">
                <div class="po-stat-label"><?php esc_html_e('Cette semaine', 'parisii-optique-plugin'); ?></div>
                <div class="po-stat-value"><?php echo esc_html((string) $week); ?></div>
            </div>
            <div class="po-stat">
                <div class="po-stat-label"><?php esc_html_e('Ce mois', 'parisii-optique-plugin'); ?></div>
                <div class="po-stat-value"><?php echo esc_html((string) $month); ?></div>
            </div>
            <div class="po-stat">
                <div class="po-stat-label"><?php esc_html_e('Cette année', 'parisii-optique-plugin'); ?></div>
                <div class="po-stat-value"><?php echo esc_html((string) $year); ?></div>
            </div>
            <div class="po-stat">
                <div class="po-stat-label"><?php esc_html_e('Total', 'parisii-optique-plugin'); ?></div>
                <div class="po-stat-value"><?php echo esc_html((string) $total); ?></div>
            </div>
        </div>
        <div class="po-contact-dashboard-footer">
            <a href="<?php echo esc_url($contact_url); ?>" class="button button-primary">
                <?php esc_html_e('Voir les messages', 'parisii-optique-plugin'); ?>
            </a>
        </div>
        <?php
    }
}
