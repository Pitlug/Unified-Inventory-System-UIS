// script to change between light and dark mode to save to local storage
document.addEventListener('DOMContentLoaded', function() {
    const modeToggle = document.getElementById('mode-toggle');
    const logoD = document.getElementById('logo');
    const logoL = document.getElementById('logo-light');
    // Toggle between light and dark mode
    function toggleMode() {
        document.documentElement.classList.toggle('dark-mode');
        
        // Save to localStorage
        if (document.documentElement.classList.contains('dark-mode')) {
            localStorage.setItem('theme-mode', 'dark');
            modeToggle.textContent = '☀️ Light Mode';
            logoD.classList.add('hidden');
            logoL.classList.remove('hidden');
        } else {
            localStorage.setItem('theme-mode', 'light');
            modeToggle.textContent = '🌙 Dark Mode';
            logoL.classList.add('hidden');
            logoD.classList.remove('hidden');
        }
    }
    
    // Load saved mode from localStorage
    function loadMode() {
        console.log(localStorage.getItem('theme-mode'));
        const savedMode = localStorage.getItem('theme-mode');
        if(modeToggle){
            if (savedMode === 'dark') {
                modeToggle.textContent = '☀️ Light Mode';
                logoD.classList.add('hidden');
                logoL.classList.remove('hidden');
                document.documentElement.classList.add('dark-mode');
            } else {
                modeToggle.textContent = '🌙 Dark Mode';
                logoL.classList.add('hidden');
                logoD.classList.remove('hidden');
                document.documentElement.classList.remove('dark-mode');
            }
        }
    }
    
    // Add click event listener
    if (!modeToggle) {
        console.log('Mode toggle button not found');
    }else{
        modeToggle.addEventListener('click', function(e) {
            console.log('Clicked Mode Toggle');
            e.preventDefault();
            toggleMode();
        });
    }
    loadMode();
});