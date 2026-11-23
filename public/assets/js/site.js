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

// Analytics consent and conversion events. GA4 only loads after an explicit
// accept, unless consent is disabled in Admin Settings.
(function () {
    const config = window.albaAnalytics;
    if (!config) return;

    const STORAGE_KEY = 'albatech_analytics_consent_v1';
    const banner = document.getElementById('consent-banner');
    let choice = null;
    try {
        choice = window.localStorage.getItem(STORAGE_KEY);
    } catch (error) {
        choice = null;
    }

    const load = function () {
        if (window.__albatechGaLoaded) return;
        window.__albatechGaLoaded = true;
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () {
            window.dataLayer.push(arguments);
        };
        window.gtag('consent', 'default', {
            analytics_storage: 'denied',
            ad_storage: 'denied',
            ad_user_data: 'denied',
            ad_personalization: 'denied'
        });
        window.gtag('js', new Date());
        window.gtag('consent', 'update', { analytics_storage: 'granted' });
        if (config.measurementId) window.gtag('config', config.measurementId);
        const script = document.createElement('script');
        script.async = true;
        script.src = config.scriptUrl;
        document.head.appendChild(script);
    };

    const showBanner = function () {
        if (banner) banner.hidden = false;
    };

    const hideBanner = function () {
        if (banner) banner.hidden = true;
    };

    const save = function (value) {
        choice = value;
        try {
            window.localStorage.setItem(STORAGE_KEY, value);
        } catch (error) {}
        if (value === 'granted') {
            load();
        } else if (window.__albatechGaLoaded) {
            window.gtag('consent', 'update', { analytics_storage: 'denied' });
        }
        hideBanner();
    };

    if (!config.consentRequired || choice === 'granted') {
        load();
    } else if (choice !== 'denied') {
        showBanner();
    }

    if (banner) {
        banner.addEventListener('click', function (event) {
            const button = event.target.closest('[data-consent]');
            if (!button) return;
            save(button.getAttribute('data-consent') === 'granted' ? 'granted' : 'denied');
        });
    }

    document.querySelectorAll('[data-consent-settings]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            showBanner();
            const accept = banner ? banner.querySelector('[data-consent="granted"]') : null;
            if (accept) accept.focus();
        });
    });

    const track = function (name, params) {
        if (!window.__albatechGaLoaded || typeof window.gtag !== 'function') return;
        window.gtag('event', name, params);
    };

    document.addEventListener('click', function (event) {
        const link = event.target.closest ? event.target.closest('a') : null;
        if (!link) return;
        const href = link.getAttribute('href') || '';
        const text = (link.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 80);
        const pagePath = window.location.pathname;
        if (href.indexOf('wa.me/') !== -1) {
            track('whatsapp_click', { link_text: text, page_path: pagePath });
        } else if (href.indexOf('tel:') === 0) {
            track('contact_click', { method: 'phone', page_path: pagePath });
        } else if (href.indexOf('mailto:') === 0) {
            track('contact_click', { method: 'email', page_path: pagePath });
        } else if (link.classList.contains('btn-primary') || link.classList.contains('btn-accent') || link.classList.contains('footer-contact-cta') || link.hasAttribute('data-analytics-cta')) {
            track('cta_click', { link_text: text, page_path: pagePath });
        }
    }, true);

    try {
        const params = new URLSearchParams(window.location.search);
        if (params.get('sent') === '1') {
            const key = 'albatech_lead_reported';
            if (!window.sessionStorage.getItem(key)) {
                window.sessionStorage.setItem(key, '1');
                track('generate_lead', { form: 'enquiry', page_path: window.location.pathname });
            }
        }
    } catch (error) {}
})();
