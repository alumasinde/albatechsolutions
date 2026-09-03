/**
 * Initializes the Quill WYSIWYG editor on any admin form that has an
 * #editor container and a hidden #content-input textarea, and syncs
 * the editor's HTML into that textarea right before the form submits.
 *
 * Kept as an external file (not an inline <script>) so it works under
 * a strict Content-Security-Policy that does not allow 'unsafe-inline'
 * for script-src.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var editorEl = document.getElementById('editor');
        var inputEl = document.getElementById('content-input');

        if (!editorEl || !inputEl || typeof Quill === 'undefined') {
            return;
        }

        var quill = new Quill('#editor', { theme: 'snow' });
        var form = editorEl.closest('form');

        if (form) {
            form.addEventListener('submit', function () {
                inputEl.value = quill.root.innerHTML;
            });
        }
    });
})();
