<?php
/**
 * Update plugin button component.
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Update_Plugin_Button {
    private const ACTION = 'update_plugin';
    private const NONCE_ACTION = 'parisii_optique_update_plugin';
    private const NONCE_NAME = 'parisii_optique_update_nonce';

    /**
     * Render the plugin update button form.
     *
     * @param array $args Component arguments.
     */
    public static function render($args = array()) {
        $args = wp_parse_args(
            $args,
            array(
                'label'           => __('Mise à jour', 'parisii-optique-plugin'),
                'wrapper_class'   => 'alignright',
                'form_class'      => 'parisii-optique-update-plugin-form',
                'button_class'    => 'button button-secondary parisii-optique-update-plugin-button',
                'confirm_message' => __('Êtes-vous sûr de vouloir mettre à jour le plugin depuis le thème ?', 'parisii-optique-plugin'),
            )
        );

        $wrapper_class = trim((string) $args['wrapper_class']);

        if ($wrapper_class !== '') {
            echo '<div class="' . esc_attr($wrapper_class) . '">';
        }
        ?>
        <form method="post" class="<?php echo esc_attr($args['form_class']); ?>">
            <?php wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME); ?>
            <input type="hidden" name="action" value="<?php echo esc_attr(self::ACTION); ?>">
            <button type="submit" class="<?php echo esc_attr($args['button_class']); ?>" onclick="return confirm('<?php echo esc_js($args['confirm_message']); ?>');">
                <span class="dashicons dashicons-update" aria-hidden="true"></span>
                <span class="parisii-optique-update-plugin-button-label"><?php echo esc_html($args['label']); ?></span>
            </button>
        </form>
        <?php
        if ($wrapper_class !== '') {
            echo '</div>';
        }
    }
}
