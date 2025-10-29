document.addEventListener('DOMContentLoaded', function () {
    const profilToggle = document.getElementById('profilToggle');
    const navMenu = document.getElementById('navMenu');

    if (profilToggle && navMenu) {
        // Toggle menu au clic
        profilToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            const isActive = navMenu.classList.toggle('active');
            profilToggle.setAttribute('aria-expanded', isActive);
        });

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function (e) {
            if (!navMenu.contains(e.target) && !profilToggle.contains(e.target)) {
                navMenu.classList.remove('active');
                profilToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Fermer le menu avec la touche Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
                profilToggle.setAttribute('aria-expanded', 'false');
                profilToggle.focus();
            }
        });
    }
});