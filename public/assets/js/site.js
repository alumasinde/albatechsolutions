// Mobile navigation toggle for the public site.
(function () {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const siteNav = document.getElementById('site-nav');
    if (!menuToggle || !siteNav) return;

    const setIcon = function (name) {
        const use = menuToggle.querySelector('svg use');
        if (use) use.setAttribute('href', '/assets/images/icons.svg#icon-' + name);
    };

    const close = function () {
        siteNav.classList.remove('is-open');
        menuToggle.setAttribute('aria-expanded', 'false');
        setIcon('bars');
    };

    menuToggle.addEventListener('click', function () {
        const isOpen = siteNav.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        setIcon(isOpen ? 'xmark' : 'bars');
    });

    siteNav.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', close);
    });

    document.addEventListener('click', function (e) {
        if (siteNav.classList.contains('is-open') && !siteNav.contains(e.target) && !menuToggle.contains(e.target)) {
            close();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && siteNav.classList.contains('is-open')) {
            close();
            menuToggle.focus();
        }
    });
})();
