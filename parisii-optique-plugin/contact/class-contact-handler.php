<?php
/**
 * Contact form handler: AJAX, validation, DB insert, email
 *
 * @package Parisii_Optique_Plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Contact_Handler {

    const TRANSIENT_CAPTCHA = 'parisii_contact_captcha_';
    const CAPTCHA_LENGTH = 8;

    public function __construct() {
        add_action('wp_ajax_parisii_contact_submit', array($this, 'handle_submit'));
        add_action('wp_ajax_nopriv_parisii_contact_submit', array($this, 'handle_submit'));
        add_action('wp_ajax_parisii_contact_captcha', array($this, 'get_captcha'));
        add_action('wp_ajax_nopriv_parisii_contact_captcha', array($this, 'get_captcha'));
    }

    /**
     * Generate random captcha (a-Z, 0-9, specials)
     */
    public static function generate_captcha() {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*';
        $len = strlen($chars);
        $code = '';
        for ($i = 0; $i < self::CAPTCHA_LENGTH; $i++) {
            $code .= $chars[wp_rand(0, $len - 1)];
        }
        return $code;
    }

    public function get_captcha() {
        check_ajax_referer('parisii_contact_nonce', 'nonce');
        $code = self::generate_captcha();
        $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
        if (!$key) {
            wp_send_json_error(array('message' => __('Clé captcha manquante.', 'parisii-optique-plugin')));
        }
        set_transient(self::TRANSIENT_CAPTCHA . $key, $code, 600); // 10 min
        wp_send_json_success(array('code' => $code));
    }

    /** Max lengths matching DB schema (varchar 255, 50; message text). */
    const MAX_NOM = 255;
    const MAX_PRENOM = 255;
    const MAX_EMAIL = 255;
    const MAX_TEL = 50;
    const MAX_SUJET = 255;
    const MAX_MESSAGE = 65535;

    public function handle_submit() {
        check_ajax_referer('parisii_contact_nonce', 'nonce');
        
        $errors = array();
        $nom = isset($_POST['nom']) ? trim(sanitize_text_field($_POST['nom'])) : '';
        $prenom = isset($_POST['prenom']) ? trim(sanitize_text_field($_POST['prenom'])) : '';
        $email = isset($_POST['email']) ? trim(sanitize_email($_POST['email'])) : '';
        $tel = isset($_POST['tel']) ? trim(sanitize_text_field($_POST['tel'])) : '';
        $sujet = isset($_POST['sujet']) ? trim(sanitize_text_field($_POST['sujet'])) : '';
        $message = isset($_POST['message']) ? trim(sanitize_textarea_field($_POST['message'])) : '';
        $captcha_input = isset($_POST['captcha']) ? sanitize_text_field($_POST['captcha']) : '';
        $captcha_key = isset($_POST['captcha_key']) ? sanitize_text_field($_POST['captcha_key']) : '';

        if (strlen($nom) < 1) {
            $errors['nom'] = __('Le nom est obligatoire.', 'parisii-optique-plugin');
        } elseif (mb_strlen($nom) > self::MAX_NOM) {
            $errors['nom'] = sprintf(__('Le nom ne doit pas dépasser %d caractères.', 'parisii-optique-plugin'), self::MAX_NOM);
        }
        if (strlen($prenom) < 1) {
            $errors['prenom'] = __('Le prénom est obligatoire.', 'parisii-optique-plugin');
        } elseif (mb_strlen($prenom) > self::MAX_PRENOM) {
            $errors['prenom'] = sprintf(__('Le prénom ne doit pas dépasser %d caractères.', 'parisii-optique-plugin'), self::MAX_PRENOM);
        }
        if (!is_email($email)) {
            $errors['email'] = __('Email invalide.', 'parisii-optique-plugin');
        } elseif (strlen($email) > self::MAX_EMAIL) {
            $errors['email'] = sprintf(__('L\'email ne doit pas dépasser %d caractères.', 'parisii-optique-plugin'), self::MAX_EMAIL);
        }
        if (strlen($tel) < 1) {
            $errors['tel'] = __('Le téléphone est obligatoire.', 'parisii-optique-plugin');
        } elseif (mb_strlen($tel) > self::MAX_TEL) {
            $errors['tel'] = sprintf(__('Le téléphone ne doit pas dépasser %d caractères.', 'parisii-optique-plugin'), self::MAX_TEL);
        }
        if (strlen($sujet) < 1) {
            $errors['sujet'] = __('Le sujet est obligatoire.', 'parisii-optique-plugin');
        } elseif (mb_strlen($sujet) > self::MAX_SUJET) {
            $errors['sujet'] = sprintf(__('Le sujet ne doit pas dépasser %d caractères.', 'parisii-optique-plugin'), self::MAX_SUJET);
        }
        if (strlen($message) < 1) {
            $errors['message'] = __('Le message est obligatoire.', 'parisii-optique-plugin');
        } elseif (mb_strlen($message) > self::MAX_MESSAGE) {
            $errors['message'] = sprintf(__('Le message ne doit pas dépasser %d caractères.', 'parisii-optique-plugin'), self::MAX_MESSAGE);
        }
        $stored = $captcha_key ? get_transient(self::TRANSIENT_CAPTCHA . $captcha_key) : false;
        if (!$captcha_key || $stored === false) {
            $errors['captcha'] = __('Code de vérification expiré ou invalide.', 'parisii-optique-plugin');
        } elseif (strcmp((string) $stored, (string) $captcha_input) !== 0) {
            $errors['captcha'] = __('Le code saisi est incorrect.', 'parisii-optique-plugin');
        }
        if ($captcha_key) {
            delete_transient(self::TRANSIENT_CAPTCHA . $captcha_key);
        }

        if (!empty($errors)) {
            wp_send_json_error(array('errors' => $errors));
        }

        $nom = strtoupper($nom);
        $prenom = ucwords(mb_strtolower($prenom));

        global $wpdb;
        $table = $wpdb->prefix . 'po_contact';
        $inserted = $wpdb->insert(
            $table,
            array(
                'nom'     => $nom,
                'prenom'  => $prenom,
                'email'   => $email,
                'tel'     => $tel,
                'sujet'   => $sujet,
                'message' => $message,
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );

        if ($inserted === false) {
            wp_send_json_error(array('message' => __('Erreur lors de l\'enregistrement. Veuillez réessayer.', 'parisii-optique-plugin')));
        }

        $mail_sent = $this->send_notification_emails($nom, $prenom, $email, $tel, $sujet, $message);

        wp_send_json_success(array(
            'message' => __('Votre message a bien été envoyé. Nous vous recontacterons rapidement.', 'parisii-optique-plugin'),
            'mail_sent' => $mail_sent,
        ));
    }

    private function send_notification_emails($nom, $prenom, $email, $tel, $sujet, $message) {
        $to_admins = array();
        $admins = get_users(array('role' => 'administrator', 'fields' => array('user_email')));
        foreach ($admins as $admin) {
            $to_admins[] = $admin->user_email;
        }
        $to_settings = Parisii_Optique_Contact_Settings::get_emails();
        $to = array_unique(array_merge($to_admins, $to_settings));
        $to = array_filter($to);
        if (empty($to)) {
            return false;
        }
        $subject = sprintf(
            '[%s] %s - %s %s',
            get_bloginfo('name'),
            str_replace(array("\r", "\n"), '', $sujet),
            str_replace(array("\r", "\n"), '', $prenom),
            str_replace(array("\r", "\n"), '', $nom)
        );
        $date_str = date_i18n(get_option('date_format') . ' à ' . get_option('time_format'), current_time('timestamp'));
        $body_html = $this->build_email_card_html($date_str, $nom, $prenom, $email, $tel, $sujet, $message);
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To: ' . $email,
        );
        return wp_mail($to, $subject, $body_html, $headers);
    }

    /**
     * Build HTML card for notification email (card style, no ID)
     */
    private function build_email_card_html($date_str, $nom, $prenom, $email, $tel, $sujet, $message) {
        $label_style = 'font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #1d2327; margin: 0 0 4px 0;';
        $value_style = 'color: #1d2327; margin: 0 0 12px 0; line-height: 1.5;';
        $link_style = 'color: #2271b1; text-decoration: underline;';
        $divider_style = 'border-bottom: 1px solid #f0f0f1; padding-bottom: 12px; margin-bottom: 12px;';
        $card_style = 'max-width: 640px; background: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,.08); padding: 20px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px;';

        $rows = array(
            array(__('Date', 'parisii-optique-plugin'), esc_html($date_str)),
            array(__('Nom', 'parisii-optique-plugin'), esc_html($nom)),
            array(__('Prénom', 'parisii-optique-plugin'), esc_html($prenom)),
            array(__('Email', 'parisii-optique-plugin'), '<a href="mailto:' . esc_attr($email) . '" style="' . $link_style . '">' . esc_html($email) . '</a>'),
            array(__('Tél', 'parisii-optique-plugin'), esc_html($tel)),
            array(__('Sujet', 'parisii-optique-plugin'), esc_html($sujet)),
            array(__('Message', 'parisii-optique-plugin'), nl2br(esc_html($message))),
        );

        $html = '<div style="' . $card_style . '">';
        foreach ($rows as $i => $row) {
            $block_style = $i < count($rows) - 1 ? $divider_style : '';
            $html .= '<div style="' . $block_style . '">';
            $html .= '<div style="' . $label_style . '">' . esc_html($row[0]) . '</div>';
            $html .= '<div style="' . $value_style . '">' . $row[1] . '</div>';
            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }
}
