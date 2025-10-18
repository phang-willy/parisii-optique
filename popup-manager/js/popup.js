/**
 * Popup Manager JavaScript
 *
 * @package Parisii_Optique
 */

(function($) {
    'use strict';

    // Initialize popups when document is ready
    $(document).ready(function() {
        $('.parisii-popup').each(function() {
            initPopup($(this));
        });
    });

    /**
     * Initialize a popup
     */
    function initPopup($popup) {
        const triggerType = $popup.data('trigger');
        const delay = parseInt($popup.data('delay')) * 1000;
        const popupId = $popup.data('popup-id');
        
        // Set popup type for CSS
        const popupType = $popup.find('.popup-content').hasClass('banner') ? 'banner' : 
                         $popup.find('.popup-content').hasClass('slide-in') ? 'slide_in' : 'modal';
        $popup.attr('data-type', popupType);
        
        // Bind close events
        bindCloseEvents($popup);
        
        // Trigger popup based on type
        switch (triggerType) {
            case 'page_load':
                setTimeout(() => showPopup($popup), delay);
                break;
                
            case 'scroll':
                $(window).on('scroll', function() {
                    if ($(window).scrollTop() > $(window).height() * 0.5) {
                        showPopup($popup);
                        $(window).off('scroll');
                    }
                });
                break;
                
            case 'click':
                // This would be triggered by a specific element
                // Implementation depends on the specific use case
                break;
                
            case 'exit_intent':
                $(document).on('mouseleave', function(e) {
                    if (e.clientY <= 0) {
                        showPopup($popup);
                        $(document).off('mouseleave');
                    }
                });
                break;
        }
    }

    /**
     * Show popup
     */
    function showPopup($popup) {
        if ($popup.hasClass('show')) return;
        
        $popup.removeClass('hidden').addClass('show');
        
        // Focus management for accessibility
        $popup.find('.popup-content').focus();
        
        // Prevent body scroll
        $('body').addClass('popup-open');
        
        // Track popup view
        trackPopupEvent($popup, 'view');
    }

    /**
     * Hide popup
     */
    function hidePopup($popup) {
        if (!$popup.hasClass('show')) return;
        
        $popup.addClass('hide');
        
        // Remove popup after animation
        setTimeout(() => {
            $popup.removeClass('show hide').addClass('hidden');
            $('body').removeClass('popup-open');
        }, 300);
        
        // Track popup close
        trackPopupEvent($popup, 'close');
    }

    /**
     * Bind close events
     */
    function bindCloseEvents($popup) {
        const popupId = $popup.data('popup-id');
        
        // Close button
        $popup.find('.popup-close').on('click', function(e) {
            e.preventDefault();
            hidePopup($popup);
            markPopupAsClosed(popupId);
        });
        
        // Overlay close
        $popup.find('.popup-overlay-close').on('click', function(e) {
            if (e.target === this) {
                hidePopup($popup);
                markPopupAsClosed(popupId);
            }
        });
        
        // ESC key
        $(document).on('keydown.popup-' + popupId, function(e) {
            if (e.keyCode === 27 && $popup.hasClass('show')) {
                hidePopup($popup);
                markPopupAsClosed(popupId);
            }
        });
    }

    /**
     * Mark popup as closed (for show once functionality)
     */
    function markPopupAsClosed(popupId) {
        $.ajax({
            url: parisii_popup_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'parisii_popup_action',
                action_type: 'close',
                popup_id: popupId,
                nonce: parisii_popup_ajax.nonce
            }
        });
    }

    /**
     * Track popup events
     */
    function trackPopupEvent($popup, event) {
        const popupId = $popup.data('popup-id');
        
        // Google Analytics tracking (if available)
        if (typeof gtag !== 'undefined') {
            gtag('event', 'popup_' + event, {
                'popup_id': popupId,
                'popup_title': $popup.find('h3').text()
            });
        }
        
        // Custom tracking
        $.ajax({
            url: parisii_popup_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'parisii_popup_action',
                action_type: 'track',
                event: event,
                popup_id: popupId,
                nonce: parisii_popup_ajax.nonce
            }
        });
    }

    /**
     * Public API
     */
    window.ParisiiPopup = {
        show: function(popupId) {
            const $popup = $('#parisii-popup-' + popupId);
            if ($popup.length) {
                showPopup($popup);
            }
        },
        
        hide: function(popupId) {
            const $popup = $('#parisii-popup-' + popupId);
            if ($popup.length) {
                hidePopup($popup);
            }
        },
        
        hideAll: function() {
            $('.parisii-popup.show').each(function() {
                hidePopup($(this));
            });
        }
    };

    // Handle page visibility change
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Pause any running timers or animations
            $('.parisii-popup').addClass('paused');
        } else {
            // Resume timers or animations
            $('.parisii-popup').removeClass('paused');
        }
    });

    // Handle window resize
    $(window).on('resize', function() {
        // Reposition popups if needed
        $('.parisii-popup.show').each(function() {
            const $popup = $(this);
            const $content = $popup.find('.popup-content');
            
            // Recalculate position for center popups
            if ($content.hasClass('center')) {
                $content.css({
                    'top': '50%',
                    'left': '50%',
                    'transform': 'translate(-50%, -50%)'
                });
            }
        });
    });

})(jQuery);
