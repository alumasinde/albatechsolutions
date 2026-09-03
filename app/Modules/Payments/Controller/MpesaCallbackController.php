<?php

declare(strict_types=1);

namespace App\Modules\Payments\Controller;

use App\Core\Logger;
use App\Core\Response;
use App\Modules\Payments\Service\PaymentService;

final class MpesaCallbackController
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {
    }

    /**
     * Safaricom posts a JSON body here — never a browser request, so
     * this route deliberately sits outside auth/CSRF middleware. We
     * read the raw body directly rather than relying on Request's
     * form-parsing, since Daraja's content-type header isn't always
     * exactly application/x-www-form-urlencoded or application/json
     * in a way our generic Request class expects.
     */
    public function handle(): Response
    {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!is_array($payload)) {
            Logger::warning('M-Pesa callback: could not parse body', ['raw' => $raw]);

            // Still return 200 — Safaricom will keep retrying on
            // non-200 responses, which we don't want for a bad payload.
            return Response::json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        $this->paymentService->handleMpesaCallback($payload);

        // Daraja expects this exact acknowledgement shape regardless
        // of what we internally did with the payload.
        return Response::json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}
