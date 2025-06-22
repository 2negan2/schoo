document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('theme-toggle');

    // Function to set the theme
    function setTheme(theme) {
        const themeIcon = document.getElementById('theme-icon');
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }

    // Initialize theme
    const currentTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

    if (currentTheme) {
        setTheme(currentTheme);
    } else if (prefersDark) {
        setTheme('dark'); // Default to system preference if no localStorage
    } else {
        setTheme('light'); // Default to light if no preference or localStorage
    }

    // Event listener for theme toggle button
    themeToggle.addEventListener('click', () => {
        let newTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    });

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) { // Only change if user hasn't manually set a theme
            setTheme(e.matches ? 'dark' : 'light');
        }
    });
});