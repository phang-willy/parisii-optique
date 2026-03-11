<?php
/**
 * Contact settings page
 *
 * @package Parisii_Optique_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Contact_Settings {

    const OPTION_EMAILS = 'parisii_optique_contact_emails';

    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        register_setting(
            'parisii_optique_contact',
            self::OPTION_EMAILS,
            array(
                'type'              => 'string',
                'sanitize_callback' => array($this, 'sanitize_emails'),
            )
        );
    }

    public function sanitize_emails($value) {
        $emails = array_map('trim', explode(',', (string) $value));
        $emails = array_filter($emails);
        $valid = array();
        foreach ($emails as $email) {
            if (is_email($email)) {
                $valid[] = $email;
            }
        }
        return implode(', ', $valid);
    }

    public static function get_emails() {
        $value = get_option(self::OPTION_EMAILS, '');
        $emails = array_map('trim', explode(',', $value));
        return array_filter($emails);
    }

    public function render_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Réglages Contact', 'parisii-optique-plugin'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('parisii_optique_contact'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="<?php echo esc_attr(self::OPTION_EMAILS); ?>">
                                <?php esc_html_e('Emails de notification', 'parisii-optique-plugin'); ?>
                            </label>
                        </th>
                        <td>
                            <textarea name="<?php echo esc_attr(self::OPTION_EMAILS); ?>"
                                      id="<?php echo esc_attr(self::OPTION_EMAILS); ?>"
                                      rows="3" class="large-text"
                                      placeholder="contact@exemple.fr, autre@exemple.fr"><?php echo esc_textarea(get_option(self::OPTION_EMAILS, '')); ?></textarea>
                            <p class="description"><?php esc_html_e('Adresses séparées par des virgules. Ces emails recevront une copie de chaque message de contact.', 'parisii-optique-plugin'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
