// Contents for public/js/admin-sidebar.js
const toggleButton = document.getElementById('menu-toggle-btn');
const sidebar = document.querySelector('.sidebar');
const mainContent = document.querySelector('.main-content');

if (toggleButton && sidebar && mainContent) {
    toggleButton.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    });
}