/**
 * AlbaTech design system - vanilla JS UI helpers.
 * Exposes window.Toast, window.Modal, window.confirmDialog.
 */
(function () {
    'use strict';

    // --- Toasts --------------------------------------------------------
    const toastContainer = document.getElementById('toast-container');

    function toast(message, type = 'success', duration = 4000) {
        if (!toastContainer) return;
        const icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', warning: 'fa-triangle-exclamation', info: 'fa-circle-info' };
        const el = document.createElement('div');
        el.className = `toast ${type}`;
        el.setAttribute('role', type === 'error' ? 'alert' : 'status');
        const icon = document.createElement('i');
        icon.className = `fa-solid ${icons[type] || icons.info}`;
        icon.setAttribute('aria-hidden', 'true');
        const text = document.createElement('span');
        text.textContent = String(message ?? '');
        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn btn-icon btn-ghost';
        close.setAttribute('aria-label', 'Dismiss notification');
        close.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
        close.addEventListener('click', () => el.remove());
        el.append(icon, text, close);
        toastContainer.appendChild(el);
        setTimeout(() => { el.style.opacity = '0'; el.style.transform = 'translateY(-4px)'; el.style.transition = 'opacity .2s ease, transform .2s ease'; setTimeout(() => el.remove(), 220); }, duration);
    }

    // --- Modal ------------------------------------------------------------
    let activeModal = null;

    function openModal({ title, body, confirmLabel = 'Confirm', cancelLabel = 'Cancel', onConfirm, danger = false }) {
        if (activeModal) activeModal.close();
        const backdrop = document.createElement('div');
        backdrop.className = 'ui-modal-backdrop';
        const modal = document.createElement('section');
        modal.className = 'ui-modal';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        const heading = document.createElement('h2');
        heading.className = 'ui-modal__title';
        heading.id = 'ui-modal-title';
        heading.textContent = String(title || 'Dialog');
        modal.setAttribute('aria-labelledby', 'ui-modal-title');
        const header = document.createElement('div'); header.className = 'ui-modal__header';
        const closeButton = document.createElement('button'); closeButton.type = 'button'; closeButton.className = 'ui-modal__close'; closeButton.setAttribute('aria-label','Close dialog'); closeButton.innerHTML = '<i class="fa-solid fa-xmark" aria-hidden="true"></i>';
        header.append(heading, closeButton);
        const bodyEl = document.createElement('div'); bodyEl.className = 'ui-modal__body'; if (body instanceof Node) bodyEl.appendChild(body); else bodyEl.innerHTML = String(body ?? '');
        const footer = document.createElement('div'); footer.className = 'ui-modal__footer';
        const cancel = document.createElement('button'); cancel.type='button'; cancel.className='btn btn-secondary'; cancel.textContent=cancelLabel; footer.appendChild(cancel);
        const confirm = document.createElement('button'); confirm.type='button'; confirm.className=`btn ${danger ? 'btn-danger' : 'btn-primary'}`; confirm.textContent=confirmLabel; footer.appendChild(confirm);
        modal.append(header, bodyEl, footer); backdrop.appendChild(modal); document.body.appendChild(backdrop);
        const previousOverflow = document.body.style.overflow; document.body.classList.add('is-modal-open'); document.body.style.overflow='hidden';
        const focusable=()=>[...modal.querySelectorAll('button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])')].filter(x=>!x.disabled);
        const previousFocus=document.activeElement;
        function close(){ if(!backdrop.isConnected)return; backdrop.remove(); document.body.classList.remove('is-modal-open'); document.body.style.overflow=previousOverflow; document.removeEventListener('keydown',onKeydown); if(activeModal?.backdrop===backdrop)activeModal=null; if(previousFocus?.focus)previousFocus.focus(); }
        function onKeydown(e){ if(e.key==='Escape'){e.preventDefault();close();return;} if(e.key==='Tab'){const items=focusable();if(!items.length)return;if(e.shiftKey&&document.activeElement===items[0]){e.preventDefault();items.at(-1).focus();}else if(!e.shiftKey&&document.activeElement===items.at(-1)){e.preventDefault();items[0].focus();}} }
        closeButton.addEventListener('click',close); cancel.addEventListener('click',close); backdrop.addEventListener('click',e=>{if(e.target===backdrop)close();}); confirm.addEventListener('click',()=>{if(typeof onConfirm==='function')onConfirm();close();}); document.addEventListener('keydown',onKeydown);
        activeModal={close,backdrop}; setTimeout(()=>focusable()[0]?.focus(),0); return activeModal;
    }

    function confirmDialog(message, onConfirm, options = {}) {
        return openModal({
            title: options.title || 'Are you sure?',
            body: (() => { const p = document.createElement('p'); p.textContent = String(message ?? ''); return p; })(),
            confirmLabel: options.confirmLabel || 'Yes, continue',
            cancelLabel: options.cancelLabel || 'Cancel',
            danger: options.danger || false,
            onConfirm,
        });
    }

    // --- Public WhatsApp service modal --------------------------------------
    // Keeps the public conversion flow short while letting a visitor add
    // context before opening WhatsApp. The service name is supplied by the
    // page data attribute; the configured number is never hardcoded.
    function openWhatsAppComposer({ intent = 'I would like to discuss a project.', context = '', number = '', placeholder = 'Tell me a little about what you need...' } = {}) {
        const cleanNumber = String(number || '').replace(/\D/g, '');
        if (!cleanNumber) return;

        const body = document.createElement('div');
        body.className = 'wa-composer';
        const intro = document.createElement('p');
        intro.className = 'wa-composer__intro';
        intro.textContent = context ? `I can include “${context}” in your message so you do not have to explain where you came from.` : 'Add a little context if you want. You can keep it short — we can discuss the rest on WhatsApp.';

        const chips = document.createElement('div');
        chips.className = 'wa-composer__context';
        ['I want a quote', 'I have a project idea', 'I need help choosing'].forEach(label => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'wa-composer__chip';
            chip.textContent = label;
            chip.addEventListener('click', () => { textarea.value = textarea.value ? textarea.value + ' ' + label + '.' : label + '.'; textarea.focus(); updateCount(); });
            chips.appendChild(chip);
        });

        const textarea = document.createElement('textarea');
        textarea.className = 'wa-composer__textarea';
        textarea.maxLength = 800;
        textarea.placeholder = placeholder;
        textarea.setAttribute('aria-label', 'Message for AlbaTech Solutions');

        const meta = document.createElement('div');
        meta.className = 'wa-composer__meta';
        const hint = document.createElement('span');
        hint.textContent = 'Optional';
        const count = document.createElement('span');
        meta.append(hint, count);
        function updateCount() { count.textContent = `${textarea.value.length}/800`; }
        textarea.addEventListener('input', updateCount);
        updateCount();

        const privacy = document.createElement('p');
        privacy.className = 'wa-composer__privacy';
        privacy.innerHTML = '<i class="fa-solid fa-lock" aria-hidden="true"></i> No account or website form is required. WhatsApp opens in a new tab.';
        body.append(intro, chips, textarea, meta, privacy);

        return openModal({
            title: 'Start a conversation',
            body,
            confirmLabel: 'Continue to WhatsApp',
            cancelLabel: 'Not now',
            onConfirm: () => {
                const note = textarea.value.trim();
                const business = (document.body?.dataset.businessName || 'AlbaTech Solutions').trim();
                let message = `Hi ${business}, ${intent}`;
                if (context) message += `\n\nI am interested in: ${context}.`;
                if (note) message += `\n\nHere is what I need:\n${note}`;
                const source = window.location.pathname;
                if (source && source !== '/') message += `\n\n(Source: ${source})`;
                const url = `https://wa.me/${cleanNumber}?text=${encodeURIComponent(message.slice(0, 1800))}`;
                if (typeof window.gtag === 'function') window.gtag('event', 'whatsapp_start', { intent, context: context || undefined, page_path: source });
                window.open(url, '_blank', 'noopener,noreferrer');
            }
        });
    }

    function openWhatsAppServiceModal(serviceName, number) {
        return openWhatsAppComposer({ intent: `I am interested in ${serviceName}.`, context: serviceName, number });
    }

    document.addEventListener('click', function (event) {
        const back = event.target.closest('[data-back]');
        if (back) {
            const fallback = back.getAttribute('data-back') || '/';
            if (document.referrer && new URL(document.referrer, window.location.href).origin === window.location.origin) { event.preventDefault(); window.history.back(); }
            else if (back.tagName === 'A') back.setAttribute('href', fallback);
            return;
        }
        const trigger = event.target.closest('.js-whatsapp, .js-whatsapp-service');
        if (!trigger) return;
        const number = (trigger.dataset.whatsappNumber || '').replace(/\D/g, '');
        if (!number) return;
        event.preventDefault();
        if (trigger.classList.contains('js-whatsapp-service')) {
            openWhatsAppServiceModal(trigger.dataset.serviceName || 'this service', number);
            return;
        }
        openWhatsAppComposer({
            intent: trigger.dataset.whatsappIntent || 'I would like to discuss a project.',
            context: trigger.dataset.whatsappContext || '',
            number,
            placeholder: trigger.dataset.whatsappPlaceholder || 'Tell me a little about what you need...'
        });
    });

    // --- Optional Google Analytics 4 bootstrap -----------------------------
    // The measurement ID is read from the database-backed page attribute so
    // no tracking identifier is hardcoded into the application source.
    const ga4Id = document.body ? document.body.dataset.ga4 : '';
    if (ga4Id) {
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function () { window.dataLayer.push(arguments); };
        window.gtag('js', new Date());
        window.gtag('config', ga4Id);
        const gaScript = document.createElement('script');
        gaScript.async = true;
        gaScript.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(ga4Id);
        document.head.appendChild(gaScript);
    }

    // --- Loading button state ------------------------------------------------
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');

        if (submitBtn && !submitBtn.disabled) {
            submitBtn.disabled = true;
            submitBtn.dataset.originalLabel = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Please wait...';
        }
    });

    // Lightweight analytics hooks. gtag is only used when GA4 is configured.
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (!link || typeof window.gtag !== 'function') return;
        const href = link.getAttribute('href') || '';
        let eventName = null;
        if (href.startsWith('https://wa.me/')) eventName = 'whatsapp_click';
        else if (href === '/contact' || href.startsWith('/contact?')) eventName = 'contact_cta_click';
        else if (href === '/projects' || href.startsWith('/projects/')) eventName = 'project_click';
        else if (href === '/services' || href.startsWith('/services/')) eventName = 'service_click';
        else if (link.target === '_blank' && /^https?:\/\//i.test(href)) eventName = 'outbound_click';
        if (eventName) window.gtag('event', eventName, { link_url: href, link_text: (link.textContent || '').trim().slice(0, 80) });
    });

    window.Toast = { show: toast };
    window.Modal = { open: openModal };
    window.confirmDialog = confirmDialog;

    // --- Mobile nav toggles (admin sidebar drawer + public site nav) --------
    function setupToggle(toggleId, targetId, overlayId) {
        var toggle = document.getElementById(toggleId);
        var target = document.getElementById(targetId);
        var overlay = overlayId ? document.getElementById(overlayId) : null;
        if (!toggle || !target) return;
        var icon = toggle.querySelector('i');
        var previousFocus = null;
        function close() {
            var wasOpen = target.classList.contains('is-open');
            target.classList.remove('is-open');
            if (toggleId === 'site-nav-toggle') target.setAttribute('aria-hidden', 'true');
            if (overlay) overlay.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
            if (icon && toggleId === 'site-nav-toggle') icon.className = 'fa-solid fa-bars';
            if (wasOpen && previousFocus && typeof previousFocus.focus === 'function') previousFocus.focus();
            previousFocus = null;
        }
        function open() {
            previousFocus = document.activeElement;
            target.classList.add('is-open');
            if (toggleId === 'site-nav-toggle') target.setAttribute('aria-hidden', 'false');
            if (overlay) overlay.classList.add('is-open');
            toggle.setAttribute('aria-expanded', 'true');
            if (icon && toggleId === 'site-nav-toggle') icon.className = 'fa-solid fa-xmark';
            var firstLink = target.querySelector('a');
            if (firstLink && window.innerWidth <= 768) setTimeout(function () { firstLink.focus(); }, 0);
        }
        toggle.addEventListener('click', function () { target.classList.contains('is-open') ? close() : open(); });
        if (overlay) overlay.addEventListener('click', close);
        target.querySelectorAll('a').forEach(function (link) { link.addEventListener('click', close); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && target.classList.contains('is-open')) { e.preventDefault(); close(); } });
        window.addEventListener('resize', function () { if (window.innerWidth > 768 && target.classList.contains('is-open')) close(); });
    }
    setupToggle('admin-nav-toggle', 'admin-sidebar', 'admin-sidebar-overlay');
    setupToggle('site-nav-toggle', 'site-nav', null);

    // --- Public reveal animations -----------------------------------------
    // Adds motion only after JS is ready, so a failed script never hides content.
    (function setupPublicReveal() {
        if (document.body) document.body.classList.add('js-ready');
        var targets = document.querySelectorAll('.public-section, .phase4-hero, .phase4-proof, .phase4-final-cta, .phase6-about-hero, .phase6-contact-hero');
        if (!targets.length) return;
        targets.forEach(function (el) {
            if (el.classList.contains('phase4-hero') || el.classList.contains('phase6-about-hero') || el.classList.contains('phase6-contact-hero')) {
                el.classList.add('phase7-reveal');
            }
            var grid = el.querySelector('.phase4-service-grid, .phase4-project-grid, .phase4-process, .phase4-testimonials, .phase6-trust-grid, .phase6-mini-projects, .phase6-testimonials');
            if (grid) grid.classList.add('phase7-stagger');
        });
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.phase7-reveal, .phase7-stagger').forEach(function (el) { el.classList.add('is-visible'); });
            return;
        }
        var observer = new IntersectionObserver(function (entries, obs) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
        document.querySelectorAll('.phase7-reveal, .phase7-stagger').forEach(function (el) { observer.observe(el); });
    })();


    // --- Core Web Vitals ---------------------------------------------------
    (function setupWebVitals() {
        if (typeof window.gtag !== 'function' || !('PerformanceObserver' in window)) return;
        var sent = {};
        function send(name, value) {
            if (sent[name]) return;
            sent[name] = true;
            window.gtag('event', name, { value: Math.round(value), metric_value: value });
        }
        try {
            var lcp = new PerformanceObserver(function (list) {
                var entries = list.getEntries(); var last = entries[entries.length - 1];
                if (last) send('web_vital_lcp', last.startTime);
            });
            lcp.observe({ type: 'largest-contentful-paint', buffered: true });
        } catch (_) {}
        try {
            var clsValue = 0;
            var cls = new PerformanceObserver(function (list) {
                list.getEntries().forEach(function (entry) { if (!entry.hadRecentInput) clsValue += entry.value; });
            });
            cls.observe({ type: 'layout-shift', buffered: true });
            window.addEventListener('pagehide', function () { send('web_vital_cls', clsValue); }, { once: true });
        } catch (_) {}
        try {
            var inp = new PerformanceObserver(function (list) {
                var worst = list.getEntries().reduce(function (max, entry) { return Math.max(max, entry.duration || 0); }, 0);
                if (worst) send('web_vital_inp', worst);
            });
            inp.observe({ type: 'event', buffered: true, durationThreshold: 40 });
        } catch (_) {}
    })();

    // --- Public homepage counters ----------------------------------------
    (function setupHomepageCounters() {
        var counters = document.querySelectorAll('.phase4-count[data-count]');
        if (!counters.length || !('IntersectionObserver' in window)) return;
        var observer = new IntersectionObserver(function(entries, obs) {
            entries.forEach(function(entry) {
                if (!entry.isIntersecting) return;
                var el = entry.target;
                var target = parseInt(el.getAttribute('data-count') || '0', 10);
                var start = performance.now();
                var duration = 800;
                function tick(now) {
                    var progress = Math.min((now - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = String(Math.round(target * eased));
                    if (progress < 1) requestAnimationFrame(tick);
                }
                requestAnimationFrame(tick);
                obs.unobserve(el);
            });
        }, { threshold: 0.45 });
        counters.forEach(function(counter) { observer.observe(counter); });
    })();

    // --- Services catalogue: category filter + live search -------------
    // Markup (services-index.php) has always shipped data-service-filter /
    // data-service-search / data-service-group / data-service-card /
    // data-service-no-results attributes and CSS for the pressed state,
    // but nothing ever listened for the click/input events. This was that
    // missing piece.
    (function setupCatalogueFilter() {
        var toolbar = document.querySelector('[data-catalogue-toolbar]');
        if (!toolbar) return;

        var filterButtons = Array.prototype.slice.call(document.querySelectorAll('[data-service-filter]'));
        var groups = Array.prototype.slice.call(document.querySelectorAll('[data-service-group]'));
        var searchInput = document.querySelector('[data-service-search]');
        var noResults = document.querySelector('[data-service-no-results]');

        var activeCategory = 'all';
        var searchTerm = '';

        function applyFilters() {
            var anyVisible = false;

            groups.forEach(function (group) {
                var groupCategory = group.getAttribute('data-category');
                var categoryMatches = activeCategory === 'all' || groupCategory === activeCategory;

                var cards = Array.prototype.slice.call(group.querySelectorAll('[data-service-card]'));
                var groupHasVisibleCard = false;

                cards.forEach(function (card) {
                    var name = (card.getAttribute('data-service-name') || '').toLowerCase();
                    var searchMatches = !searchTerm || name.indexOf(searchTerm) !== -1;
                    var visible = categoryMatches && searchMatches;
                    card.hidden = !visible;
                    if (visible) groupHasVisibleCard = true;
                });

                group.hidden = !groupHasVisibleCard;
                if (groupHasVisibleCard) anyVisible = true;
            });

            if (noResults) noResults.hidden = anyVisible;
        }

        filterButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                activeCategory = button.getAttribute('data-service-filter') || 'all';
                filterButtons.forEach(function (btn) {
                    btn.setAttribute('aria-pressed', btn === button ? 'true' : 'false');
                });
                applyFilters();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                searchTerm = searchInput.value.trim().toLowerCase();
                // A live text search should show matches across every
                // category, not just the one currently selected.
                if (searchTerm && activeCategory !== 'all') {
                    activeCategory = 'all';
                    filterButtons.forEach(function (btn) {
                        btn.setAttribute('aria-pressed', btn.getAttribute('data-service-filter') === 'all' ? 'true' : 'false');
                    });
                }
                applyFilters();
            });
        }
    })();
})();
