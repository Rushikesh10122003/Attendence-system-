// Dark Mode Toggle Script

// Check saved preference
window.onload = function(){
    var mode = localStorage.getItem('darkMode');
    if(mode === 'dark'){
        document.body.classList.add('dark-mode');
        document.getElementById('darkToggle').innerText = '☀️';
    } else {
        document.getElementById('darkToggle').innerText = '🌙';
    }
}

// Toggle Function
function toggleDarkMode(){
    var body   = document.body;
    var toggle = document.getElementById('darkToggle');

    if(body.classList.contains('dark-mode')){
        body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'light');
        toggle.innerText = '🌙';
    } else {
        body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'dark');
        toggle.innerText = '☀️';
    }
}