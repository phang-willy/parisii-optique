/**
 * Front-end default image editor (media library)
 *
 * @package Parisii_Optique
 */

(function ($) {
    'use strict';

    if (typeof parisiiDefaultImage === 'undefined') {
        return;
    }

    var mediaFrame = null;

    function saveImage($figure, attachmentId) {
        return $.post(parisiiDefaultImage.ajaxUrl, {
            action: 'parisii_save_default_image',
            nonce: parisiiDefaultImage.nonce,
            post_id: $figure.data('post-id'),
            slot: $figure.data('image-slot'),
            attachment_id: attachmentId,
            width: $figure.data('width') || 1024,
            height: $figure.data('height') || 1024,
            size: $figure.data('size') || 'large',
            class: $figure.data('class') || 'wp-image-40',
            figure_class: $figure.data('figure-class') || 'wp-block-image size-large',
            alt: $figure.data('alt') || ''
        });
    }

    function replaceFigure($figure, html) {
        var $next = $(html);
        $figure.replaceWith($next);
    }

    function openMedia($figure) {
        if (typeof wp === 'undefined' || !wp.media) {
            return;
        }

        if (mediaFrame) {
            mediaFrame.off('select');
        }

        mediaFrame = wp.media({
            title: parisiiDefaultImage.i18n.title,
            button: { text: parisiiDefaultImage.i18n.button },
            library: { type: 'image' },
            multiple: false
        });

        mediaFrame.on('select', function () {
            var attachment = mediaFrame.state().get('selection').first().toJSON();
            $figure.addClass('is-saving');

            saveImage($figure, attachment.id)
                .done(function (response) {
                    if (!response || !response.success) {
                        window.alert(parisiiDefaultImage.i18n.error);
                        return;
                    }
                    replaceFigure($figure, response.data.html);
                })
                .fail(function () {
                    window.alert(parisiiDefaultImage.i18n.error);
                })
                .always(function () {
                    $figure.removeClass('is-saving');
                });
        });

        mediaFrame.open();
    }

    $(document).on('click', '.parisii-default-image__replace', function (e) {
        e.preventDefault();
        openMedia($(this).closest('.parisii-default-image'));
    });

    $(document).on('click', '.parisii-default-image__reset', function (e) {
        e.preventDefault();
        var $figure = $(this).closest('.parisii-default-image');
        $figure.addClass('is-saving');

        saveImage($figure, 0)
            .done(function (response) {
                if (!response || !response.success) {
                    window.alert(parisiiDefaultImage.i18n.error);
                    return;
                }
                replaceFigure($figure, response.data.html);
            })
            .fail(function () {
                window.alert(parisiiDefaultImage.i18n.error);
            })
            .always(function () {
                $figure.removeClass('is-saving');
            });
    });
})(jQuery);
