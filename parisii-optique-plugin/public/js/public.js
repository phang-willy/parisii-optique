/**
 * Public JavaScript for Parisii Optique Plugin
 *
 * @package Parisii_Optique_Plugin
 */

jQuery(document).ready(function($) {
    
    /**
     * Smooth scroll to brands section after filter
     */
    if (window.location.search.includes('brand_search') || window.location.search.includes('brand_categories')) {
        $('html, body').animate({
            scrollTop: $('.parisii-optique-brands-content').offset().top - 100
        }, 500);
    }
    
    /**
     * Handle category checkbox changes
     */
    $('.parisii-optique-category-checkboxes input[type="checkbox"]').on('change', function() {
        // You can add any additional functionality here if needed
    });
    
});

