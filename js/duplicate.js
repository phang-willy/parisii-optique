/**
 * Duplicate Content JavaScript functionality
 *
 * @package Parisii_Optique
 */

(function($) {
    'use strict';

    /**
     * Initialize duplicate links
     */
    function initDuplicateLinks() {
        $('.duplicate-link').on('click', function(e) {
            e.preventDefault();
            
            const $link = $(this);
            const confirmText = $link.data('confirm');
            const redirectUrl = $link.data('redirect');
            const duplicateUrl = $link.attr('href');
            
            // Show confirmation if needed
            if (confirmText && !confirm(confirmText)) {
                return;
            }
            
            // Show loading state
            $link.addClass('loading').prop('disabled', true);
            const originalText = $link.html();
            $link.html('<span class="dashicons dashicons-update" style="font-size: 16px; vertical-align: middle; animation: spin 1s linear infinite;"></span> Duplication...');
            
            // Perform duplicate action
            $.ajax({
                url: duplicateUrl,
                type: 'GET',
                success: function(response) {
                    // Show success message
                    showDuplicateMessage('Élément dupliqué avec succès !', 'success');
                    
                    // Redirect if specified
                    if (redirectUrl) {
                        setTimeout(function() {
                            window.location.href = redirectUrl;
                        }, 1000);
                    }
                },
                error: function(xhr, status, error) {
                    // Show error message
                    showDuplicateMessage('Erreur lors de la duplication : ' + error, 'error');
                },
                complete: function() {
                    // Reset button state
                    $link.removeClass('loading').prop('disabled', false);
                    $link.html(originalText);
                }
            });
        });
    }

    /**
     * Initialize duplicate multiple functionality
     */
    function initDuplicateMultiple() {
        $('.duplicate-multiple-btn').on('click', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const urls = $btn.data('urls');
            const redirectUrl = $btn.data('redirect');
            
            if (!urls || urls.length === 0) {
                showDuplicateMessage('Aucun élément à dupliquer', 'error');
                return;
            }
            
            // Show confirmation
            if (!confirm('Êtes-vous sûr de vouloir dupliquer ' + urls.length + ' éléments ?')) {
                return;
            }
            
            // Show progress
            const $progress = $btn.siblings('.duplicate-progress');
            const $progressFill = $progress.find('.progress-fill');
            const $progressText = $progress.find('.progress-text');
            
            $progress.show();
            $btn.prop('disabled', true);
            
            let completed = 0;
            let errors = 0;
            
            // Process each URL
            urls.forEach(function(url, index) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        completed++;
                        updateProgress(completed, urls.length, $progressFill, $progressText);
                    },
                    error: function(xhr, status, error) {
                        completed++;
                        errors++;
                        updateProgress(completed, urls.length, $progressFill, $progressText);
                    },
                    complete: function() {
                        if (completed === urls.length) {
                            // All done
                            setTimeout(function() {
                                $progress.hide();
                                $btn.prop('disabled', false);
                                
                                if (errors === 0) {
                                    showDuplicateMessage('Tous les éléments ont été dupliqués avec succès !', 'success');
                                } else {
                                    showDuplicateMessage(completed + ' éléments dupliqués, ' + errors + ' erreurs', 'warning');
                                }
                                
                                // Redirect if specified
                                if (redirectUrl) {
                                    setTimeout(function() {
                                        window.location.href = redirectUrl;
                                    }, 2000);
                                }
                            }, 500);
                        }
                    }
                });
            });
        });
    }

    /**
     * Update progress bar
     */
    function updateProgress(completed, total, $progressFill, $progressText) {
        const percentage = Math.round((completed / total) * 100);
        $progressFill.css('width', percentage + '%');
        $progressText.text(percentage + '%');
    }

    /**
     * Show duplicate message
     */
    function showDuplicateMessage(message, type) {
        const $message = $('<div class="duplicate-message duplicate-message-' + type + '">' + message + '</div>');
        
        // Remove existing messages
        $('.duplicate-message').remove();
        
        // Add new message
        $('body').append($message);
        
        // Show message with animation
        $message.hide().fadeIn(300);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $message.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Add duplicate button to admin bar
     */
    function addDuplicateToAdminBar() {
        if (!$('#wp-admin-bar-root-default').length) {
            return;
        }
        
        // Only add to singular pages
        if (!is_singular()) {
            return;
        }
        
        const postId = $('body').data('post-id');
        if (!postId) {
            return;
        }
        
        const postType = $('body').data('post-type') || 'post';
        const duplicateUrl = getDuplicateUrl(postId, postType);
        
        if (duplicateUrl) {
            const $duplicateItem = $('<li id="wp-admin-bar-duplicate">' +
                '<a href="' + duplicateUrl + '" class="ab-item" data-confirm="Dupliquer cette page ?">' +
                '<span class="ab-icon dashicons dashicons-admin-page"></span>' +
                '<span class="ab-label">Dupliquer</span>' +
                '</a>' +
                '</li>');
            
            $('#wp-admin-bar-root-default').append($duplicateItem);
            
            // Add click handler
            $duplicateItem.find('a').on('click', function(e) {
                e.preventDefault();
                
                if (confirm('Dupliquer cette page ?')) {
                    window.location.href = $(this).attr('href');
                }
            });
        }
    }

    /**
     * Get duplicate URL for post
     */
    function getDuplicateUrl(postId, postType) {
        // This would need to be implemented with proper nonce generation
        // For now, return null to disable this feature
        return null;
    }

    /**
     * Add duplicate buttons to post lists
     */
    function addDuplicateToPostLists() {
        // Add duplicate buttons to post rows
        $('.wp-list-table tbody tr').each(function() {
            const $row = $(this);
            const postId = $row.find('.check-column input[type="checkbox"]').val();
            const postType = $row.find('.post_type').text() || 'post';
            
            if (postId) {
                const duplicateUrl = getDuplicateUrl(postId, postType);
                if (duplicateUrl) {
                    const $duplicateLink = $('<a href="' + duplicateUrl + '" class="duplicate-row-link" title="Dupliquer">' +
                        '<span class="dashicons dashicons-admin-page"></span>' +
                        '</a>');
                    
                    $row.find('.row-actions').append(' | <span class="duplicate">' + duplicateUrl + '</span>');
                }
            }
        });
    }

    /**
     * Add keyboard shortcuts
     */
    function addKeyboardShortcuts() {
        $(document).on('keydown', function(e) {
            // Ctrl+D to duplicate current page (for logged-in users)
            if (e.ctrlKey && e.key === 'd' && is_singular()) {
                e.preventDefault();
                
                const postId = $('body').data('post-id');
                if (postId) {
                    const duplicateUrl = getDuplicateUrl(postId, 'post');
                    if (duplicateUrl) {
                        window.location.href = duplicateUrl;
                    }
                }
            }
        });
    }

    /**
     * Check if current page is singular
     */
    function is_singular() {
        return $('body').hasClass('single') || $('body').hasClass('page');
    }

    /**
     * Initialize all duplicate functionality
     */
    function initAllDuplicate() {
        initDuplicateLinks();
        initDuplicateMultiple();
        addDuplicateToAdminBar();
        addDuplicateToPostLists();
        addKeyboardShortcuts();
    }

    // Initialize when document is ready
    $(document).ready(initAllDuplicate);

    /**
     * Public API
     */
    window.ParisiiDuplicate = {
        duplicate: function(postId, postType) {
            const duplicateUrl = getDuplicateUrl(postId, postType);
            if (duplicateUrl) {
                window.location.href = duplicateUrl;
            }
        },
        
        duplicateMultiple: function() {
            return false;
        },
        
        showMessage: function(message, type) {
            showDuplicateMessage(message, type);
        }
    };

})(jQuery);
