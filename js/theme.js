/**
 * Theme JavaScript functionality
 *
 * @package Parisii_Optique
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        initMobileMenu();
        initScrollToTop();
        initSmoothScroll();
        initAnimations();
        initDesktopMenu();
        adjustNavbarForAdminBar();
    });

    // Window resize
    $(window).on('resize', function() {
        adjustNavbarForAdminBar();
    });

    /**
     * Adjust navbar position and main padding for WordPress admin bar
     */
    function adjustNavbarForAdminBar() {
        const $adminBar = $('#wpadminbar');
        const $header = $('header#header');
        const $main = $('main#main');

        if (!$adminBar.length) return;

        // Récupérer la position de l'admin bar (distance par rapport au haut de la page)
        $adminBar.offset() ? $adminBar.offset().top : 0;

        const adminBarHeight = $adminBar.outerHeight() || 0;
        const headerHeight = $header.outerHeight() || 0 ;
        
        if (adminBarHeight > 0) {
            $adminBar.css('position', 'fixed');
            $header.css('top', adminBarHeight + 'px');
            $main.css('padding-top', (headerHeight + adminBarHeight) + 'px');
        }
    }

    /**
     * Desktop menu dropdown functionality
     */
    function initDesktopMenu() {
        // S'assurer que tous les items avec sous-menus ont la classe menu-item-has-children
        $('.navbar nav > ul > li').each(function() {
            const $item = $(this);
            const $submenu = $item.find('> ul.dropdown-menu, > ul.sub-menu');
            
            if ($submenu.length > 0 && !$item.hasClass('menu-item-has-children')) {
                $item.addClass('menu-item-has-children');
            }
        });

        // Supprimer toutes les icônes dashicons du front
        $('.navbar .dashicons, .navbar .menu-item-icon').remove();
        
        // Gérer les sous-menus desktop avec toggle
        initDesktopSubmenus();
        
        // Fermer les dropdowns quand on clique ailleurs
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.navbar nav > ul > li').length) {
                $('.navbar .desktop-submenu-toggle[aria-expanded="true"]').each(function() {
                    closeDesktopSubmenu($(this));
                });
                $('.navbar .desktop-submenu-toggle-nested[aria-expanded="true"]').each(function() {
                    closeDesktopSubmenu($(this));
                });
            }
        });
    }
    
    /**
     * Initialize desktop submenu toggles
     */
    function initDesktopSubmenus() {
        // Gérer les sous-menus de niveau 0
        $('.navbar .desktop-submenu-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $button = $(this);
            const isExpanded = $button.attr('aria-expanded') === 'true';
            
            // Fermer tous les autres dropdowns ouverts
            $('.navbar .desktop-submenu-toggle[aria-expanded="true"]').not($button).each(function() {
                closeDesktopSubmenu($(this));
            });
            
            if (isExpanded) {
                closeDesktopSubmenu($button);
            } else {
                openDesktopSubmenu($button);
            }
        });
        
        // Gérer les sous-sous-menus (niveau 1+)
        $('.navbar .desktop-submenu-toggle-nested').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $button = $(this);
            const isExpanded = $button.attr('aria-expanded') === 'true';
            
            // Fermer tous les autres sous-sous-menus frères
            $button.closest('ul').find('.desktop-submenu-toggle-nested[aria-expanded="true"]').not($button).each(function() {
                closeDesktopSubmenu($(this));
            });
            
            if (isExpanded) {
                closeDesktopSubmenu($button);
            } else {
                openDesktopSubmenu($button);
            }
        });
    }
    
    /**
     * Open desktop submenu
     */
    function openDesktopSubmenu($button) {
        const $submenu = $button.closest('li').find('> ul.dropdown-menu, > ul.sub-menu');
        const $chevron = $button.find('svg');
        
        $submenu.removeClass('hidden').css({
            opacity: 0, 
            visibility: 'hidden',
            pointerEvents: 'none'
        }).animate({opacity: 1}, 200, function() {
            $(this).css({
                visibility: 'visible',
                pointerEvents: 'auto'
            });
        });
        $chevron.addClass('rotate-180');
        $button.attr('aria-expanded', 'true');
        $button.attr('aria-label', 'Fermer le sous-menu');
    }
    
    /**
     * Close desktop submenu
     */
    function closeDesktopSubmenu($button) {
        const $submenu = $button.closest('li').find('> ul.dropdown-menu, > ul.sub-menu');
        const $chevron = $button.find('svg');
        
        $submenu.animate({opacity: 0}, 200, function() {
            $(this).css({
                visibility: 'hidden',
                pointerEvents: 'none'
            }).addClass('hidden');
        });
        $chevron.removeClass('rotate-180');
        $button.attr('aria-expanded', 'false');
        $button.attr('aria-label', 'Ouvrir le sous-menu');
    }

    /**
     * Mobile menu functionality
     */
    function initMobileMenu() {
        const mobileMenuButton = $('#mobile-menu-button');
        const mobileMenuClose = $('#mobile-menu-close');
        const mobileMenu = $('#mobile-menu');
        const mobileMenuOverlay = $('#mobile-menu-overlay');

        if (mobileMenuButton.length && mobileMenu.length) {
            // Ouvrir le menu
            mobileMenuButton.on('click', function() {
                openMobileMenu();
            });

            // Fermer le menu avec le bouton X
            mobileMenuClose.on('click', function() {
                closeMobileMenu();
            });

            // Fermer le menu en cliquant sur l'overlay
            mobileMenuOverlay.on('click', function() {
                closeMobileMenu();
            });

            // Fermer avec Escape
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && mobileMenu.hasClass('show')) {
                    closeMobileMenu();
                }
            });
            
            // Gérer les sous-menus mobiles avec toggle
            initMobileSubmenus();
        }
    }

    /**
     * Open mobile menu
     */
    function openMobileMenu() {
        const mobileMenu = $('#mobile-menu');
        const mobileMenuOverlay = $('#mobile-menu-overlay');
        
        // Afficher l'overlay et le menu
        mobileMenuOverlay.removeClass('hidden');
        
        // Forcer un reflow pour que la transition fonctionne
        mobileMenuOverlay[0].offsetHeight;
        mobileMenu[0].offsetHeight;
        
        // Ajouter les classes pour l'animation
        setTimeout(function() {
            mobileMenuOverlay.addClass('show');
            mobileMenu.addClass('show');
            // Rendre accessible au clavier
            mobileMenu.attr('aria-hidden', 'false').removeAttr('inert');
        }, 10);
        
        // Bloquer le scroll de la page
        $('body').css({
            overflow: 'hidden',
            position: 'fixed',
            width: '100%'
        });
    }

    /**
     * Close mobile menu
     */
    function closeMobileMenu() {
        const mobileMenu = $('#mobile-menu');
        const mobileMenuOverlay = $('#mobile-menu-overlay');
        
        // Retirer les classes d'animation
        mobileMenuOverlay.removeClass('show');
        mobileMenu.removeClass('show');
        // Rendre inaccessible au clavier
        mobileMenu.attr('aria-hidden', 'true').attr('inert', '');
        
        // Masquer l'overlay après l'animation
        setTimeout(function() {
            mobileMenuOverlay.addClass('hidden');
        }, 300);
        
        // Réactiver le scroll
        $('body').css({
            overflow: '',
            position: '',
            width: ''
        });
    }

    /**
     * Get scrollbar width
     */
    function getScrollbarWidth() {
        const outer = document.createElement('div');
        outer.style.visibility = 'hidden';
        outer.style.overflow = 'scroll';
        document.body.appendChild(outer);
        
        const inner = document.createElement('div');
        outer.appendChild(inner);
        
        const scrollbarWidth = outer.offsetWidth - inner.offsetWidth;
        outer.parentNode.removeChild(outer);
        
        return scrollbarWidth;
    }
    
    /**
     * Initialize mobile submenu toggles
     */
    function initMobileSubmenus() {
        // Initialiser l'état des chevrons pour les menus déjà ouverts
        $('#mobile-menu .submenu-toggle[aria-expanded="true"]').each(function() {
            const $button = $(this);
            const $chevron = $button.find('svg');
            $chevron.addClass('rotate-180');
            $button.attr('aria-label', 'Fermer le sous-menu');
        });
        
        $('#mobile-menu .submenu-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const $button = $(this);
            const $submenu = $button.closest('li').find('> ul.submenu-mobile');
            const $chevron = $button.find('svg');
            const isExpanded = $button.attr('aria-expanded') === 'true';
            
            if (isExpanded) {
                // Fermer le sous-menu
                $submenu.slideUp(200, function() {
                    $(this).addClass('hidden');
                });
                $chevron.removeClass('rotate-180');
                $button.attr('aria-expanded', 'false');
                $button.attr('aria-label', 'Ouvrir le sous-menu');
            } else {
                // Ouvrir le sous-menu
                $submenu.removeClass('hidden').hide().slideDown(200);
                $chevron.addClass('rotate-180');
                $button.attr('aria-expanded', 'true');
                $button.attr('aria-label', 'Fermer le sous-menu');
            }
        });
    }

    /**
     * Scroll to top functionality
     */
    function initScrollToTop() {
        const scrollToTopButton = $('#scroll-to-top');

        if (scrollToTopButton.length) {
            // Show/hide button based on scroll position
            $(window).on('scroll', function() {
                if ($(this).scrollTop() > 300) {
                    scrollToTopButton.addClass('visible');
                } else {
                    scrollToTopButton.removeClass('visible');
                }
            });

            // Smooth scroll to top on click
            scrollToTopButton.on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, 800);
            });
        }
    }

    /**
     * Smooth scroll for anchor links
     */
    function initSmoothScroll() {
        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            const target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 800);
            }
        });
    }

    /**
     * Initialize animations
     */
    function initAnimations() {
        // Intersection Observer for fade-in animations
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in-up');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe elements with animation class
            $('.animate-on-scroll').each(function() {
                observer.observe(this);
            });
        }
    }

    /**
     * Newsletter form handling
     */
    function initNewsletterForm() {
        $('.newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const email = form.find('input[type="email"]').val();
            const submitButton = form.find('button[type="submit"]');
            const originalText = submitButton.text();
            
            // Basic email validation
            if (!isValidEmail(email)) {
                showNotification('Veuillez entrer une adresse email valide.', 'error');
                return;
            }
            
            // Show loading state
            submitButton.text('Inscription...').prop('disabled', true);
            
            // Simulate AJAX request (replace with actual implementation)
            setTimeout(function() {
                showNotification('Merci pour votre inscription à notre newsletter !', 'success');
                form[0].reset();
                submitButton.text(originalText).prop('disabled', false);
            }, 1000);
        });
    }

    /**
     * Email validation
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        const notification = $(`
            <div class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' : 
                type === 'error' ? 'bg-red-500 text-white' : 
                'bg-blue-500 text-white'
            }">
                <div class="flex items-center">
                    <span class="flex-1">${message}</span>
                    <button class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `);
        
        $('body').append(notification);
        
        // Show notification
        setTimeout(function() {
            notification.removeClass('translate-x-full');
        }, 100);
        
        // Auto hide after 5 seconds
        setTimeout(function() {
            notification.addClass('translate-x-full');
            setTimeout(function() {
                notification.remove();
            }, 300);
        }, 5000);
        
        // Close button functionality
        notification.find('button').on('click', function() {
            notification.addClass('translate-x-full');
            setTimeout(function() {
                notification.remove();
            }, 300);
        });
    }

    // Initialize newsletter form when document is ready
    $(document).ready(function() {
        initNewsletterForm();
    });

})(jQuery);
