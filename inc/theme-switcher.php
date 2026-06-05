<?php
/**
 * Theme switcher functionality
 *
 * @package Parisii_Optique
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add theme switcher script to head
 */
function parisii_optique_theme_switcher_script() {
    ?>
    <script>
        // Theme switcher functionality
        (function() {
            const themeSwitcher = document.getElementById('theme-switcher');
            const html = document.documentElement;
            const currentTheme = localStorage.getItem('theme') || '<?php echo get_theme_mod('default_theme_mode', 'system'); ?>';
            
            // Apply saved theme
            function applyTheme(theme) {
                if (theme === 'dark') {
                    html.classList.add('dark');
                } else if (theme === 'light') {
                    html.classList.remove('dark');
                } else {
                    // System theme
                    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        html.classList.add('dark');
                    } else {
                        html.classList.remove('dark');
                    }
                }
            }
            
            // Initialize theme
            applyTheme(currentTheme);
            
            // Theme switcher click handler
            if (themeSwitcher) {
                themeSwitcher.addEventListener('click', function() {
                    const isDark = html.classList.contains('dark');
                    const newTheme = isDark ? 'light' : 'dark';
                    
                    localStorage.setItem('theme', newTheme);
                    applyTheme(newTheme);
                    
                    // Update button icon
                    updateThemeIcon(newTheme);
                });
            }
            
            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
                if (localStorage.getItem('theme') === 'system' || !localStorage.getItem('theme')) {
                    applyTheme('system');
                }
            });
            
            // Update theme icon
            function updateThemeIcon(theme) {
                if (!themeSwitcher) return;
                
                const icon = themeSwitcher.querySelector('svg path');
                if (!icon) return;
                
                if (theme === 'dark') {
                    // Sun icon for light mode
                    icon.setAttribute('d', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z');
                } else {
                    // Moon icon for dark mode
                    icon.setAttribute('d', 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z');
                }
            }
            
            // Initialize icon
            updateThemeIcon(currentTheme);
        })();
    </script>
    <?php
}
add_action('wp_head', 'parisii_optique_theme_switcher_script');

/**
 * Add theme switcher customizer settings
 */
function parisii_optique_theme_switcher_customize($wp_customize) {
    $wp_customize->add_setting('theme_switcher_enabled', [
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ]);
    
    $wp_customize->add_control('theme_switcher_enabled', [
        'label' => __('Activer le sélecteur de thème', 'parisii-optique'),
        'section' => 'theme_switcher',
        'type' => 'checkbox',
    ]);
}
add_action('customize_register', 'parisii_optique_theme_switcher_customize');
