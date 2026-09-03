/**
 * AlbaTech v2 theme controller.
 * CSS variables remain the source of truth. This only switches token sets.
 */
(function () {
  'use strict';
  const STORAGE_KEY = 'albatech-theme';
  const allowed = new Set(['light', 'dark', 'contrast']);

  function applyTheme(theme) {
    if (!allowed.has(theme)) theme = 'light';
    document.documentElement.dataset.theme = theme;
    try { localStorage.setItem(STORAGE_KEY, theme); } catch (_) {}
  }

  function init() {
    let theme = 'light';
    try { theme = localStorage.getItem(STORAGE_KEY) || 'light'; } catch (_) {}
    applyTheme(theme);
    document.addEventListener('click', function (event) {
      const control = event.target.closest('[data-theme]');
      if (!control) return;
      const requested = control.getAttribute('data-theme');
      if (allowed.has(requested)) applyTheme(requested);
    });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();

  window.AlbaTheme = { set: applyTheme, get: () => document.documentElement.dataset.theme || 'light' };
})();
