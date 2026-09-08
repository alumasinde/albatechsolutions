/**
 * AlbaTech blog rich editor.
 * Initializes Quill, adds secure image upload support, and syncs HTML to the
 * hidden form field before submit. Image uploads are stored in the CMS media
 * library rather than embedded as data URLs.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var editorEl = document.getElementById('editor');
        var inputEl = document.getElementById('content-input');

        if (!editorEl || !inputEl || typeof Quill === 'undefined') return;

        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ blockquote: true }],
                        ['link', 'image'],
                        [{ align: [] }],
                        ['clean']
                    ],
                    handlers: { image: function () { uploadImage(quill, editorEl); } }
                }
            }
        });

        var form = editorEl.closest('form');
        if (form) form.addEventListener('submit', function () { inputEl.value = quill.root.innerHTML; });
        initFeaturedImagePicker();
    });

    function csrfToken() {
        var token = document.querySelector('#blog-post-form input[name="_token"]');
        return token ? token.value : '';
    }

    function uploadImage(quill, editorEl) {
        var input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/jpeg,image/png,image/webp,image/svg+xml';
        input.click();
        input.addEventListener('change', function () {
            if (!input.files || !input.files[0]) return;
            var formData = new FormData();
            formData.append('image', input.files[0]);
            formData.append('_token', csrfToken());
            var range = quill.getSelection(true);
            var uploadUrl = editorEl.getAttribute('data-image-upload-url') || '/admin/blog/media';

            fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        if (!response.ok || !data.success) throw new Error(data.message || 'Image upload failed.');
                        return data;
                    });
                })
                .then(function (data) {
                    var index = range ? range.index : quill.getLength();
                    quill.insertEmbed(index, 'image', data.url, 'user');
                    quill.setSelection(index + 1, 0, 'silent');
                })
                .catch(function (error) { window.alert(error.message || 'Image upload failed.'); });
        });
    }

    function initFeaturedImagePicker() {
        var hidden = document.getElementById('featured_media_id');
        var preview = document.getElementById('featured-image-preview');
        var fileInput = document.getElementById('featured_image');
        var removeButton = document.getElementById('remove-featured-image');
        var items = document.querySelectorAll('.blog-media-item');
        if (!hidden || !preview) return;

        function setPreview(id, url, name) {
            hidden.value = id || '';
            preview.classList.remove('is-empty');
            preview.innerHTML = '<img src="' + escapeHtml(url) + '" alt=""><span>' + escapeHtml(name || 'Featured image') + '</span>';
            items.forEach(function (item) { item.classList.toggle('is-selected', item.getAttribute('data-media-id') === String(id)); });
        }

        function clearPreview() {
            hidden.value = '';
            preview.classList.add('is-empty');
            preview.innerHTML = '<span>No featured image selected</span>';
            items.forEach(function (item) { item.classList.remove('is-selected'); });
            if (fileInput) fileInput.value = '';
        }

        items.forEach(function (item) {
            item.addEventListener('click', function () {
                setPreview(item.getAttribute('data-media-id'), item.getAttribute('data-media-url'), item.getAttribute('data-media-name'));
                if (fileInput) fileInput.value = '';
            });
        });

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                if (!fileInput.files || !fileInput.files[0]) return;
                hidden.value = '';
                items.forEach(function (item) { item.classList.remove('is-selected'); });
                var file = fileInput.files[0];
                var reader = new FileReader();
                reader.onload = function (event) {
                    preview.classList.remove('is-empty');
                    preview.innerHTML = '<img src="' + event.target.result + '" alt=""><span>' + escapeHtml(file.name) + ' — will upload when saved</span>';
                };
                reader.readAsDataURL(file);
            });
        }
        if (removeButton) removeButton.addEventListener('click', clearPreview);
    }

    function escapeHtml(value) {
        return String(value || '').replace(/[&<>"']/g, function (character) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character];
        });
    }
})();
