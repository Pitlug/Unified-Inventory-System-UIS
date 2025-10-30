// script to change between light and dark mode to save to local storage

document.addEventListener('DOMContentLoaded', function() {
    const modeToggle = document.getElementById('mode-toggle');
    
    if (!modeToggle) {
        console.log('Mode toggle button not found - waiting for page load');
        return;
    }
    
    console.log('Mode toggle initialized');
    
    // Toggle between light and dark mode
    function toggleMode() {
        document.body.classList.toggle('dark-mode');
        
        // Save to localStorage
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme-mode', 'dark');
            modeToggle.textContent = '☀️ Light Mode';
        } else {
            localStorage.setItem('theme-mode', 'light');
            modeToggle.textContent = '🌙 Dark Mode';
        }
    }
    
    // Load saved mode from localStorage
    function loadMode() {
        const savedMode = localStorage.getItem('theme-mode');
        if (savedMode === 'dark') {
            document.body.classList.add('dark-mode');
            modeToggle.textContent = '☀️ Light Mode';
        } else {
            modeToggle.textContent = '🌙 Dark Mode';
        }
    }
    
    // Add click event listener
    modeToggle.addEventListener('click', function(e) {
        e.preventDefault();
        toggleMode();
    });
    
    // Load the saved mode on page load
    loadMode();
});