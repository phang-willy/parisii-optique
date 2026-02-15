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
    
    // Media uploader for brand logo (bouton "Télécharger une image")
    $(document).on('click', '.parisii-optique-upload-image', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var cell = button.closest('td');
        var input = cell.find('#brand_logo');
        var preview = cell.find('.parisii-optique-logo-preview');
        
        if (typeof wp === 'undefined' || !wp.media) {
            return;
        }
        
        var frame = wp.media({
            title: 'Sélectionner un logo',
            button: {
                text: 'Utiliser cette image'
            },
            library: { type: 'image' },
            multiple: false
        });
        
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            input.val(attachment.url);
            if (preview.length) {
                preview.html('<img src="' + attachment.url + '" alt="" style="max-width: 150px; max-height: 150px; margin-top: 10px;">').show();
            } else {
                button.after('<p class="parisii-optique-logo-preview"><img src="' + attachment.url + '" alt="" style="max-width: 150px; max-height: 150px; margin-top: 10px;"></p>');
            }
        });
        
        frame.open();
    });
    
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