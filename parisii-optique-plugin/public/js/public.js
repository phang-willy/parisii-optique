/**
 * Public JavaScript for Parisii Optique Plugin
 *
 * @package Parisii_Optique_Plugin
 */

jQuery(document).ready(function($) {
    
    /**
     * Smooth scroll to brands section after filter
     */
    if (window.location.search.includes('brand_') && $('.parisii-optique-brands-content').length) {
        $('html, body').animate({
            scrollTop: $('.parisii-optique-brands-content').offset().top - 100
        }, 500);
    }
    
});

