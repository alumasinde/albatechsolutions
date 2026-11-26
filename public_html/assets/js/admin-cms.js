(function () {
  'use strict';
  const nav = Array.from(document.querySelectorAll('[data-cms-target]'));
  const panels = Array.from(document.querySelectorAll('[data-cms-panel]'));
  const pageSelect = document.getElementById('cms-page-select');
  const pageEditors = Array.from(document.querySelectorAll('[data-cms-page]'));
  const pageStatus = document.getElementById('cms-page-status');

  function showPage(slug) {
    let found = false;
    pageEditors.forEach(function (editor) {
      const active = Boolean(slug) && editor.dataset.cmsPage === slug;
      editor.hidden = !active;
      editor.open = active;
      found = found || active;
    });
    if (pageStatus) {
      pageStatus.textContent = found
        ? 'Editing /' + slug + '. Changes apply only to this page.'
        : 'Choose a page to open its editor.';
    }
  }

  function showPanel(name, updateHash) {
    if (!panels.some(function (panel) { return panel.dataset.cmsPanel === name; })) name = 'homepage';
    panels.forEach(function (panel) {
      const active = panel.dataset.cmsPanel === name;
      panel.hidden = !active;
      panel.classList.toggle('is-current', active);
    });
    nav.forEach(function (button) {
      const active = button.dataset.cmsTarget === name;
      button.classList.toggle('is-active', active);
      if (active) button.setAttribute('aria-current', 'page');
      else button.removeAttribute('aria-current');
    });
    if (updateHash) history.replaceState(null, '', '#' + name);
    if (name === 'pages' && pageSelect && pageSelect.value) showPage(pageSelect.value);
    const current = panels.find(function (panel) { return panel.dataset.cmsPanel === name; });
    if (current) current.scrollIntoView({ block: 'start', behavior: 'smooth' });
  }

  nav.forEach(function (button) {
    button.addEventListener('click', function () { showPanel(button.dataset.cmsTarget, true); });
  });
  if (pageSelect) pageSelect.addEventListener('change', function () { showPage(pageSelect.value); });

  const initial = (window.location.hash || '').slice(1);
  showPanel(['homepage', 'pages', 'navigation', 'ctas'].includes(initial) ? initial : 'homepage', false);
  showPage(pageSelect ? pageSelect.value : '');
})();
