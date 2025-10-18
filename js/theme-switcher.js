/**
 * Theme Switcher
 * Simple and effective dark mode switching
 */

(function() {
    'use strict';

    // Theme management
    const THEME_KEY = 'parisii-theme';
    const THEMES = ['light', 'dark', 'system'];
    
    // Get current theme
    function getTheme() {
        return localStorage.getItem(THEME_KEY) || 'system';
    }
    
    // Set theme
    function setTheme(theme) {
        if (!THEMES.includes(theme)) return;
        
        localStorage.setItem(THEME_KEY, theme);
        applyTheme(theme);
        updateUI();
    }
    
    // Apply theme to document
    function applyTheme(theme) {
        const html = document.documentElement;
        
        // Remove existing theme classes
        html.classList.remove('light', 'dark');
        
        let finalTheme = theme;
        if (theme === 'system') {
            // Use system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            finalTheme = prefersDark ? 'dark' : 'light';
        }
        
        html.classList.add(finalTheme);
        html.setAttribute('data-theme', finalTheme); // support DaisyUI & co
    }
    
    // Update UI elements
    function updateUI() {
        const currentTheme = getTheme();
        const html = document.documentElement;
        const isDark = html.classList.contains('dark');
        
        // Update all theme switcher buttons
        document.querySelectorAll('.theme-switcher-btn').forEach(button => {
            const sunIcon = button.querySelector('.sun-icon');
            const moonIcon = button.querySelector('.moon-icon');
            const systemIcon = button.querySelector('.system-icon');
            
            // Reset all icons
            [sunIcon, moonIcon, systemIcon].forEach(icon => {
                if (icon) {
                    icon.style.display = 'none';
                    icon.style.visibility = 'hidden';
                }
            });
            
            // Show appropriate icon based on current theme setting
            if (currentTheme === 'system' && systemIcon) {
                systemIcon.style.display = 'block';
                systemIcon.style.visibility = 'visible';
            } else if (currentTheme === 'dark' && moonIcon) {
                moonIcon.style.display = 'block';
                moonIcon.style.visibility = 'visible';
            } else if (sunIcon) {
                sunIcon.style.display = 'block';
                sunIcon.style.visibility = 'visible';
            }
        });
        
        // Update dropdown menus
        document.querySelectorAll('.theme-dropdown-item').forEach(item => {
            item.classList.remove('active');
            if (item.dataset.theme === currentTheme) {
                item.classList.add('active');
            }
        });
        
        // Update toggle switches
        document.querySelectorAll('.theme-switcher-toggle').forEach(toggle => {
            toggle.checked = isDark;
        });
        
        // Update select dropdowns
        document.querySelectorAll('.theme-switcher-select').forEach(select => {
            select.value = currentTheme;
        });
    }
    
    // Cycle through themes
    function cycleTheme() {
        const currentTheme = getTheme();
        const currentIndex = THEMES.indexOf(currentTheme);
        const nextIndex = (currentIndex + 1) % THEMES.length;
        setTheme(THEMES[nextIndex]);
    }
    
    // Toggle dropdown menu
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        if (dropdown) {
            const isOpen = dropdown.classList.contains('show');
            
            // Close all other dropdowns first
            closeAllDropdowns();
            
            // If this dropdown wasn't open, open it after a small delay
            if (!isOpen) {
                setTimeout(() => {
                    dropdown.classList.remove('hidden');
                    // Force reflow
                    dropdown.offsetHeight;
                    dropdown.classList.add('show');
                }, 10);
            }
        }
    }
    
    // Close all dropdown menus
    function closeAllDropdowns() {
        document.querySelectorAll('.theme-dropdown-menu.show').forEach(dropdown => {
            dropdown.classList.remove('show');
            setTimeout(() => {
                dropdown.classList.add('hidden');
            }, 200);
        });
    }
    
    // Initialize theme on page load
    function init() {
        // Apply saved theme
        applyTheme(getTheme());
        updateUI();
        
        // Listen for system theme changes
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                if (getTheme() === 'system') {
                    applyTheme('system');
                    updateUI();
                }
            });
        }
        
        // Bind click events to theme switcher buttons
        document.addEventListener('click', (e) => {
            const button = e.target.closest('.theme-switcher-btn');
            if (button) {
                e.preventDefault();
                e.stopPropagation();
                
                // Check if it's a dropdown button
                const dropdownId = button.dataset.dropdownToggle;
                if (dropdownId) {
                    toggleDropdown(dropdownId);
                } else {
                    cycleTheme();
                }
            }
            
            // Handle dropdown item clicks
            const dropdownItem = e.target.closest('.theme-dropdown-item');
            if (dropdownItem) {
                e.preventDefault();
                e.stopPropagation();
                const theme = dropdownItem.dataset.theme;
                setTheme(theme);
                closeAllDropdowns();
            }
        });
        
        // Bind change events to toggles
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('theme-switcher-toggle')) {
                setTheme(e.target.checked ? 'dark' : 'light');
            }
        });
        
        // Bind change events to selects
        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('theme-switcher-select')) {
                setTheme(e.target.value);
            }
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.theme-switcher-component')) {
                closeAllDropdowns();
            }
        });
        
        // Close dropdowns on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAllDropdowns();
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Force theme synchronization
    function syncTheme() {
        const currentTheme = getTheme();
        applyTheme(currentTheme);
        updateUI();
        console.log('Theme synchronized:', currentTheme, '->', document.documentElement.className, 'data-theme:', document.documentElement.getAttribute('data-theme'));
    }
    
    // Force CSS refresh for light mode
    function forceLightMode() {
        setTheme('light');
        console.log('Forced light mode');
    }
    
    // Expose API globally
    window.ParisiiTheme = {
        getTheme,
        setTheme,
        cycleTheme,
        syncTheme,
        forceLightMode
    };
    
})();
