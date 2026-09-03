<?php

declare(strict_types=1);

namespace App\Modules\Payments\Gateway;

/**
 * Provider abstraction. The Payments module does not know whether the
 * underlying provider is Paystack, Daraja, Stripe, Flutterwave, etc.
 */
interface PaymentGatewayInterface
{
    /**
     * @param array{
     *   reference:string,
     *   amount:int,
     *   currency:string,
     *   email:string,
     *   phone?:string|null,
     *   description?:string|null,
     *   metadata?:array<string,mixed>,
     *   callback_url?:string|null
     * } $payment
     * @return array{success:bool, authorization_url?:string, access_code?:string, reference?:string, message?:string}
     */
    public function initialize(array $payment): array;

    /**
     * @return array{success:bool, data?:array<string,mixed>, message?:string}
     */
    public function verify(string $reference): array;

    /**
     * Initiates a refund on the provider. A full refund of the original
     * transaction unless a smaller amount is given. Refunds are async on
     * Paystack's side — a successful response here means the request was
     * accepted, not that funds have moved yet.
     *
     * @return array{success:bool, message?:string}
     */
    public function refund(string $reference, ?int $amountSubunit = null, ?string $reason = null): array;
}
