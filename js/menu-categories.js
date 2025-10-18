/**
 * Menu Categories JavaScript functionality
 *
 * @package Parisii_Optique
 */

(function($) {
    'use strict';

    // Initialize menu categories when document is ready
    $(document).ready(function() {
        initProductCategories();
        initTabs();
        initDropdowns();
        initMenuTracking();
    });

    /**
     * Initialize product categories
     */
    function initProductCategories() {
        // Add hover effects
        $('.product-category-item').hover(
            function() {
                $(this).addClass('hover');
            },
            function() {
                $(this).removeClass('hover');
            }
        );

        // Add click tracking
        $('.product-category-link').on('click', function() {
            const categoryName = $(this).find('.category-name').text().trim();
            const categoryCount = $(this).find('.category-count').text().trim();
            
            console.log('Product category clicked:', categoryName, categoryCount);
            
            // Track with analytics if available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'Product Categories',
                    'event_label': categoryName,
                    'value': categoryCount ? parseInt(categoryCount.replace(/[()]/g, '')) : 0
                });
            }
        });
    }

    /**
     * Initialize tabs functionality
     */
    function initTabs() {
        $('.product-categories-tabs').each(function() {
            const $tabs = $(this);
            const $navItems = $tabs.find('.tab-nav-item');
            const $contentItems = $tabs.find('.tab-content');

            $navItems.on('click', function(e) {
                e.preventDefault();
                
                const $clickedItem = $(this);
                const targetId = $clickedItem.find('.tab-nav-link').attr('href');
                
                // Remove active class from all items
                $navItems.removeClass('active');
                $contentItems.removeClass('active');
                
                // Add active class to clicked item
                $clickedItem.addClass('active');
                $(targetId).addClass('active');
                
                // Track tab switch
                const tabName = $clickedItem.find('.category-name').text().trim();
                console.log('Tab switched to:', tabName);
                
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'tab_switch', {
                        'event_category': 'Product Categories',
                        'event_label': tabName
                    });
                }
            });
        });
    }

    /**
     * Initialize dropdowns
     */
    function initDropdowns() {
        $('.product-categories-dropdown, .menu-dropdown').on('change', function() {
            const selectedValue = $(this).val();
            const selectedText = $(this).find('option:selected').text().trim();
            
            if (selectedValue) {
                console.log('Category selected:', selectedText);
                
                // Track selection
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'category_select', {
                        'event_category': 'Product Categories',
                        'event_label': selectedText
                    });
                }
                
                // Navigate to selected category
                window.location.href = selectedValue;
            }
        });
    }

    /**
     * Initialize menu tracking
     */
    function initMenuTracking() {
        // Track menu item clicks
        $('.menu-item-product-category a').on('click', function() {
            const categoryName = $(this).text().trim();
            const categoryUrl = $(this).attr('href');
            
            console.log('Menu category clicked:', categoryName, categoryUrl);
            
            // Track with analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'Menu',
                    'event_label': 'Product Category: ' + categoryName,
                    'value': 1
                });
            }
        });

        // Track menu hover
        $('.menu-item-product-category').hover(
            function() {
                const categoryName = $(this).find('a').text().trim();
                console.log('Menu category hovered:', categoryName);
            },
            function() {
                // Hover out
            }
        );
    }

    /**
     * Add category filtering
     */
    function initCategoryFiltering() {
        $('.category-filter').on('input', function() {
            const filterValue = $(this).val().toLowerCase();
            const $categories = $('.product-category-item');
            
            $categories.each(function() {
                const $category = $(this);
                const categoryName = $category.find('.category-name').text().toLowerCase();
                const categoryDescription = $category.find('.category-description').text().toLowerCase();
                
                if (categoryName.includes(filterValue) || categoryDescription.includes(filterValue)) {
                    $category.show();
                } else {
                    $category.hide();
                }
            });
        });
    }

    /**
     * Add category sorting
     */
    function initCategorySorting() {
        $('.category-sort').on('change', function() {
            const sortBy = $(this).val();
            const $container = $(this).closest('.product-categories-shortcode');
            const $categories = $container.find('.product-category-item');
            
            $categories.sort(function(a, b) {
                let aValue, bValue;
                
                switch (sortBy) {
                    case 'name':
                        aValue = $(a).find('.category-name').text().toLowerCase();
                        bValue = $(b).find('.category-name').text().toLowerCase();
                        return aValue.localeCompare(bValue);
                        
                    case 'count':
                        aValue = parseInt($(a).find('.category-count').text().replace(/[()]/g, '')) || 0;
                        bValue = parseInt($(b).find('.category-count').text().replace(/[()]/g, '')) || 0;
                        return bValue - aValue;
                        
                    case 'random':
                        return Math.random() - 0.5;
                        
                    default:
                        return 0;
                }
            });
            
            $container.find('.product-categories-list, .product-categories-grid').html($categories);
        });
    }

    /**
     * Add category search
     */
    function initCategorySearch() {
        $('.category-search').on('input', function() {
            const searchValue = $(this).val().toLowerCase();
            const $categories = $('.product-category-item');
            
            if (searchValue === '') {
                $categories.show();
                return;
            }
            
            $categories.each(function() {
                const $category = $(this);
                const categoryName = $category.find('.category-name').text().toLowerCase();
                const categoryDescription = $category.find('.category-description').text().toLowerCase();
                
                if (categoryName.includes(searchValue) || categoryDescription.includes(searchValue)) {
                    $category.show();
                } else {
                    $category.hide();
                }
            });
        });
    }

    /**
     * Add category lazy loading
     */
    function initCategoryLazyLoading() {
        const $lazyCategories = $('.product-category-item[data-lazy]');
        
        if ($lazyCategories.length === 0) {
            return;
        }
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const $category = $(entry.target);
                    const categoryId = $category.data('category-id');
                    
                    // Load category content
                    loadCategoryContent(categoryId, $category);
                    
                    // Stop observing this element
                    observer.unobserve(entry.target);
                }
            });
        });
        
        $lazyCategories.each(function() {
            observer.observe(this);
        });
    }

    /**
     * Load category content
     */
    function loadCategoryContent(categoryId, $category) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'parisii_load_category_content',
                category_id: categoryId,
                nonce: parisii_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $category.html(response.data);
                }
            },
            error: function() {
                console.error('Failed to load category content');
            }
        });
    }

    /**
     * Add category animations
     */
    function initCategoryAnimations() {
        // Animate categories on scroll
        const $categories = $('.product-category-item');
        
        if ($categories.length === 0) {
            return;
        }
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    $(entry.target).addClass('animate-fade-in-up');
                }
            });
        });
        
        $categories.each(function() {
            observer.observe(this);
        });
    }

    /**
     * Add category keyboard navigation
     */
    function initCategoryKeyboardNavigation() {
        $('.product-categories-shortcode').on('keydown', function(e) {
            const $categories = $(this).find('.product-category-link');
            const $focused = $(document.activeElement);
            const currentIndex = $categories.index($focused);
            
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    const nextIndex = (currentIndex + 1) % $categories.length;
                    $categories.eq(nextIndex).focus();
                    break;
                    
                case 'ArrowUp':
                    e.preventDefault();
                    const prevIndex = currentIndex > 0 ? currentIndex - 1 : $categories.length - 1;
                    $categories.eq(prevIndex).focus();
                    break;
                    
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    $focused.click();
                    break;
            }
        });
    }

    /**
     * Initialize all functionality
     */
    function initAll() {
        initProductCategories();
        initTabs();
        initDropdowns();
        initMenuTracking();
        initCategoryFiltering();
        initCategorySorting();
        initCategorySearch();
        initCategoryLazyLoading();
        initCategoryAnimations();
        initCategoryKeyboardNavigation();
    }

    // Initialize when document is ready
    $(document).ready(initAll);

    /**
     * Public API
     */
    window.ParisiiMenuCategories = {
        filter: function(selector, value) {
            $(selector).val(value).trigger('input');
        },
        
        sort: function(selector, sortBy) {
            $(selector).val(sortBy).trigger('change');
        },
        
        search: function(selector, value) {
            $(selector).val(value).trigger('input');
        },
        
        refresh: function() {
            location.reload();
        }
    };

    /**
     * Handle category events
     */
    $(document).on('category:clicked', function(event, data) {
        console.log('Category clicked event:', data);
    });
    
    $(document).on('category:filtered', function(event, data) {
        console.log('Category filtered event:', data);
    });
    
    $(document).on('category:sorted', function(event, data) {
        console.log('Category sorted event:', data);
    });

})(jQuery);
