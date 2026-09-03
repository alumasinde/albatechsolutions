/**
 * Renders the 2FA setup QR code client-side from the otpauth:// URI
 * stored in the #qr-code element's data-uri attribute. External file
 * (not inline) so it runs under a strict CSP without 'unsafe-inline'.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('qr-code');

        if (!el || typeof QRCode === 'undefined') {
            return;
        }

        var uri = el.getAttribute('data-uri');

        if (!uri) {
            return;
        }

        new QRCode(el, { text: uri, width: 200, height: 200 });
    });
})();
