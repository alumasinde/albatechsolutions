<?php

declare(strict_types=1);

namespace App\Modules\Orders\Service;

use App\Core\AuditLog;
use App\Core\BaseService;
use App\Core\Notifications\NotificationService;
use App\Modules\Orders\Repository\OrderRepository;

final class OrderService extends BaseService
{
    /**
     * Allowed forward transitions. Cancellation/decline are allowed
     * from any non-terminal state, handled separately in transition().
     */
    private const FLOW = [
        'submitted'    => ['under_review'],
        'under_review' => ['quoted'],
        'quoted'       => ['accepted', 'declined'],
        'accepted'     => ['in_progress'],
        'in_progress'  => ['completed'],
    ];

    private const TERMINAL = ['completed', 'declined', 'cancelled'];

    public function __construct(
        private readonly OrderRepository $orders,
        private readonly NotificationService $notifications
    ) {
    }

    /**
     * @return array{success: bool, id?: int, order_number?: string}
     */
    public function create(int $userId, int $serviceId, string $customerNotes): array
    {
        $tempNumber = 'TMP-' . bin2hex(random_bytes(8));

        $id = $this->orders->create([
            'order_number'   => $tempNumber,
            'user_id'        => $userId,
            'service_id'     => $serviceId,
            'status'         => 'submitted',
            'customer_notes' => $customerNotes,
        ]);

        $orderNumber = 'ORD-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
        $this->orders->setOrderNumber($id, $orderNumber);

        $this->orders->recordStatusChange($id, 'submitted', $userId, 'Order submitted by customer.');
        AuditLog::record('order.created', 'order', $id);

        return ['success' => true, 'id' => $id, 'order_number' => $orderNumber];
    }

    /**
     * @return array{success: bool, message?: string}
     */
    public function transition(int $orderId, string $newStatus, ?int $changedBy, ?string $note = null): array
    {
        $order = $this->orders->find($orderId);

        if (!$order) {
            return ['success' => false, 'message' => 'Order not found.'];
        }

        $current = $order['status'];

        if (in_array($current, self::TERMINAL, true)) {
            return ['success' => false, 'message' => 'This order is already closed and cannot be changed.'];
        }

        $isCancellation = in_array($newStatus, ['cancelled', 'declined'], true);
        $isAllowedForward = in_array($newStatus, self::FLOW[$current] ?? [], true);

        if (!$isCancellation && !$isAllowedForward) {
            return ['success' => false, 'message' => "Cannot move an order from \"{$current}\" to \"{$newStatus}\"."];
        }

        $updates = ['status' => $newStatus];
        if ($newStatus === 'completed') {
            $updates['completed_at'] = date('Y-m-d H:i:s');
        }

        $this->orders->update($orderId, $updates);
        $this->orders->recordStatusChange($orderId, $newStatus, $changedBy, $note);
        AuditLog::record('order.status_changed', 'order', $orderId, ['status' => $newStatus]);

        $this->notifyCustomer($orderId, $newStatus, $note);

        return ['success' => true];
    }

    public function setQuote(int $orderId, float $price, ?int $changedBy, ?string $note = null): array
    {
        $order = $this->orders->find($orderId);

        if (!$order || $order['status'] !== 'under_review') {
            return ['success' => false, 'message' => 'Orders can only be quoted while under review.'];
        }

        $this->orders->update($orderId, ['quoted_price' => $price]);

        return $this->transition($orderId, 'quoted', $changedBy, $note ?: "Quote set: KES " . number_format($price, 2));
    }

    public function markPaid(int $orderId, ?int $changedBy, ?string $note = null): array
    {
        $order = $this->orders->find($orderId);

        if (!$order || $order['status'] !== 'accepted') {
            return ['success' => false, 'message' => 'Only accepted orders can be marked as paid.'];
        }

        if (!empty($order['paid_at'])) {
            return ['success' => true, 'message' => 'Order was already marked as paid.'];
        }

        $this->orders->update($orderId, [
            'agreed_price' => $order['quoted_price'],
            'paid_at'      => date('Y-m-d H:i:s'),
        ]);

        AuditLog::record('order.marked_paid', 'order', $orderId);

        // Move the order forward so staff can see it's paid and ready to start,
        // and so the customer gets a payment-confirmation notification. Without
        // this, the order silently stayed "accepted" forever after payment.
        return $this->transition($orderId, 'in_progress', $changedBy, $note ?? 'Payment received.');
    }

    private function notifyCustomer(int $orderId, string $status, ?string $note = null): void
    {
        $order = $this->orders->findWithDetails($orderId);

        if (!$order) {
            return;
        }

        $messages = [
            'under_review' => 'Your order is now under review.',
            'quoted'        => sprintf('A quote of KES %s is ready for your order.', number_format((float) $order['quoted_price'], 2)),
            'accepted'      => 'You accepted the quote. We will begin work shortly.',
            'in_progress'   => sprintf('Payment received — KES %s. We have started work on your order.', number_format((float) ($order['agreed_price'] ?? $order['quoted_price']), 2)),
            'completed'     => 'Your order has been completed.',
            'declined'      => 'Your order was declined.',
            'cancelled'     => 'Your order was cancelled.',
        ];

        $body = sprintf(
            "Hi %s,\n\nUpdate on your order %s (%s):\n%s\n\nView your order: %s/orders/%d",
            $order['customer_name'],
            $order['order_number'],
            $order['service_name'],
            $messages[$status] ?? "Status updated to {$status}.",
            \App\Core\Config::get('app.url'),
            $orderId
        );

        if ($status === 'in_progress') {
            $body .= sprintf("\nDownload your receipt: %s/orders/%d/receipt", \App\Core\Config::get('app.url'), $orderId);
        }

        if ($status === 'cancelled' && $note) {
            $body .= "\n\n" . $note;
        }

        $this->notifications->notify(
            $order['customer_email'],
            "Order {$order['order_number']} update",
            $body
        );
    }
}
