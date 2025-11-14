// script to change between light and dark mode to save to local storage
document.addEventListener('DOMContentLoaded', function() {
    const modeToggle = document.getElementById('mode-toggle');

    // Toggle between light and dark mode
    function toggleMode() {
        document.body.classList.toggle('dark-mode');
        const logoD = document.getElementById('logo');
        const logoL = document.getElementById('logo-light');
        
        // Save to localStorage
        if (document.body.classList.contains('dark-mode')) {
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
        updatePHP();
    }
    
    // Load saved mode from localStorage
    function loadMode() {
        const savedMode = localStorage.getItem('theme-mode');
        if(modeToggle){
            if (savedMode === 'dark') {
                modeToggle.textContent = '☀️ Light Mode';
            } else {
                modeToggle.textContent = '🌙 Dark Mode';
            }
        }
    }

    function updatePHP(){
        document.cookie = "theme="+localStorage.getItem('theme-mode')+";Domain=";
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
    
    // Load the saved mode on page load
    console.log(document.cookie);
    updatePHP();
    loadMode();
});