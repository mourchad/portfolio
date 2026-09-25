/* ==========================================================================
   ESPACE ADMIN — Interactions légères
   ========================================================================== */
(function () {
    'use strict';

    /* Menu latéral sur mobile */
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.getElementById('sidebarToggle');
    if (sidebar && toggle) {
        toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
        document.addEventListener('click', (e) => {
            if (
                sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                !toggle.contains(e.target)
            ) {
                sidebar.classList.remove('open');
            }
        });
    }

    /* Masque le badge quand il n'y a aucun message non lu */
    document.querySelectorAll('[data-unread-badge]').forEach((badge) => {
        if (parseInt(badge.textContent.trim() || '0', 10) === 0) {
            badge.dataset.empty = 'true';
        }
    });

    /* Fermeture automatique des alertes après 6 secondes */
    document.querySelectorAll('.alert-dismissible').forEach((alert) => {
        setTimeout(() => {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }, 6000);
    });
})();
