<?php
/**
 * Sections component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Newsletter section shortcode
 */
function parisii_optique_newsletter_section_shortcode($atts, $content = null) {
    $atts = shortcode_atts([
        'title' => 'Restez informé de nos nouveautés',
        'subtitle' => 'Inscrivez-vous à notre newsletter pour recevoir nos dernières actualités et offres spéciales.',
        'placeholder' => 'Votre adresse email',
        'button_text' => 'S\'inscrire',
        'class' => '',
    ], $atts);

    ob_start();
    ?>
    <section class="parisii-newsletter-section py-16 bg-main-500 text-white <?php echo esc_attr($atts['class']); ?>">
        <div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl font-heading font-bold mb-4">
                    <?php echo esc_html($atts['title']); ?>
                </h2>
                
                <p class="text-xl mb-8 opacity-90">
                    <?php echo esc_html($atts['subtitle']); ?>
                </p>
                
                <form class="newsletter-form flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                    <input type="email" 
                           name="email" 
                           placeholder="<?php echo esc_attr($atts['placeholder']); ?>" 
                           required
                           class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50">
                    <button type="submit" 
                            class="px-6 py-3 bg-white text-main-500 font-semibold rounded-lg hover:bg-gray-100 transition-colors">
                        <?php echo esc_html($atts['button_text']); ?>
                    </button>
                </form>
                
                <?php if ($content) : ?>
                    <div class="mt-8 text-sm opacity-75">
                        <?php echo do_shortcode($content); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode('newsletter_section', 'parisii_optique_newsletter_section_shortcode');
