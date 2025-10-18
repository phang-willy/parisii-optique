/**
 * Admin JavaScript for Parisii Optique Plugin
 *
 * @package Parisii_Optique_Plugin
 */

jQuery(document).ready(function($) {
    
    // Handle tab switching
    $('.nav-tab').on('click', function(e) {
        // Remove active class from all tabs
        $('.nav-tab').removeClass('nav-tab-active');
        
        // Add active class to clicked tab
        $(this).addClass('nav-tab-active');
    });
    
    // Handle form submissions with tab preservation
    $('form').on('submit', function() {
        var currentTab = $('.nav-tab-active').attr('href').split('tab=')[1];
        if (currentTab) {
            $(this).append('<input type="hidden" name="tab" value="' + currentTab + '">');
        }
    });
    
    // Media uploader for brand logo
    if (typeof wp !== 'undefined' && wp.media) {
        $('.brand-logo-upload').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var input = button.siblings('.brand-logo-url');
            var preview = button.siblings('.brand-logo-preview');
            
            var frame = wp.media({
                title: 'Sélectionner un logo',
                button: {
                    text: 'Utiliser ce logo'
                },
                multiple: false
            });
            
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                input.val(attachment.url);
                preview.html('<img src="' + attachment.url + '" style="max-width: 150px; max-height: 150px;">');
            });
            
            frame.open();
        });
        
        // Remove logo
        $('.brand-logo-remove').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var input = button.siblings('.brand-logo-url');
            var preview = button.siblings('.brand-logo-preview');
            
            input.val('');
            preview.html('');
        });
    }
    
    // Confirm delete actions
    $('.delete-action').on('click', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
            e.preventDefault();
        }
    });
    
    // Bulk actions confirmation
    $('.bulk-action-form').on('submit', function(e) {
        var action = $(this).find('select[name="action2"]').val();
        if (action === 'bulk_delete') {
            if (!confirm('Êtes-vous sûr de vouloir supprimer les éléments sélectionnés ?')) {
                e.preventDefault();
            }
        }
    });
    
    // Auto-save form data (optional enhancement)
    $('input, textarea, select').on('change', function() {
        var form = $(this).closest('form');
        var formData = form.serialize();
        localStorage.setItem('parisii_optique_form_data', formData);
    });
    
    // Restore form data on page load
    var savedData = localStorage.getItem('parisii_optique_form_data');
    if (savedData && !$('input[name="brand_id"]').val()) { // Only restore for new brands
        var params = new URLSearchParams(savedData);
        params.forEach(function(value, key) {
            $('input[name="' + key + '"], textarea[name="' + key + '"], select[name="' + key + '"]').val(value);
        });
    }
    
    // Clear saved data on successful form submission
    $('form').on('submit', function() {
        localStorage.removeItem('parisii_optique_form_data');
    });
    
});