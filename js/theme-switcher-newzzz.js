/**
 * Theme Switcher - shadcn/ui inspired
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
        
        if (theme === 'system') {
            // Use system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            html.classList.add(prefersDark ? 'dark' : 'light');
        } else {
            html.classList.add(theme);
        }
    }
    
    // Update UI elements
    function updateUI() {
        const currentTheme = getTheme();
        const isDark = currentTheme === 'dark' || 
                      (currentTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        // Update all theme switcher buttons
        document.querySelectorAll('.theme-switcher-btn').forEach(button => {
            const sunIcon = button.querySelector('.sun-icon');
            const moonIcon = button.querySelector('.moon-icon');
            
            if (sunIcon && moonIcon) {
                // Show/hide icons based on theme
                if (isDark) {
                    sunIcon.style.display = 'none';
                    moonIcon.style.display = 'block';
                } else {
                    sunIcon.style.display = 'block';
                    moonIcon.style.display = 'none';
                }
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
            if (e.target.closest('.theme-switcher-btn')) {
                e.preventDefault();
                cycleTheme();
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
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expose API globally
    window.ParisiiTheme = {
        getTheme,
        setTheme,
        cycleTheme
    };
    
})();
