<?php

declare(strict_types=1);

namespace App\Modules\Payments\Service;

use App\Core\Settings;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Renders a payment receipt as PDF. Kept intentionally simple — one order,
 * one completed payment, one page. Company details are pulled from the
 * same settings already exposed in the admin panel, so nothing here needs
 * a separate "invoice settings" screen.
 */
final class ReceiptService
{
    public function render(array $order, array $payment): string
    {
        $dompdf = new Dompdf($this->options());
        $dompdf->loadHtml($this->html($order, $payment));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function options(): Options
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        return $options;
    }

    private function html(array $order, array $payment): string
    {
        $siteName = e(Settings::get('site_name', 'AlbaTech Solutions'));
        $contactEmail = e(Settings::get('contact_email', ''));
        $contactPhone = e(Settings::get('contact_phone', ''));
        $logoPath = Settings::get('site_logo_path', '');
        $logoUrl = $logoPath ? e(rtrim(\App\Core\Config::get('app.url'), '/') . '/' . ltrim((string) $logoPath, '/')) : null;

        $paidAt = $payment['updated_at'] ?? $payment['created_at'] ?? date('Y-m-d H:i:s');
        $method = $this->methodLabel($payment);

        $rows = [
            'Receipt No.' => 'RCPT-' . str_pad((string) $payment['id'], 6, '0', STR_PAD_LEFT),
            'Order No.' => (string) $order['order_number'],
            'Service' => (string) $order['service_name'],
            'Date Paid' => date('d M Y, H:i', strtotime((string) $paidAt)),
            'Payment Method' => $method,
            'Reference' => (string) $payment['reference'],
        ];

        $rowsHtml = '';
        foreach ($rows as $label => $value) {
            $rowsHtml .= '<tr><td class="label">' . e($label) . '</td><td class="value">' . e($value) . '</td></tr>';
        }

        $amount = number_format((float) $payment['amount'], 2);
        $currency = e((string) ($payment['currency'] ?? 'KES'));
        $logoHtml = $logoUrl ? '<img src="' . $logoUrl . '" style="max-height:56px;max-width:220px;">' : '<div class="company-name">' . $siteName . '</div>';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Helvetica, sans-serif; color: #0f172a; font-size: 12px; }
    .header { display: table; width: 100%; margin-bottom: 28px; }
    .company-name { font-size: 20px; font-weight: bold; color: #0F4C81; }
    .header-right { text-align: right; }
    .stamp { display: inline-block; padding: 6px 16px; border: 2px solid #15803d; color: #15803d; font-weight: bold; letter-spacing: 1px; border-radius: 6px; font-size: 14px; }
    h1 { font-size: 18px; margin: 0 0 4px; color: #0f172a; }
    .muted { color: #64748b; }
    table.details { width: 100%; border-collapse: collapse; margin: 24px 0; }
    table.details td { padding: 9px 0; border-bottom: 1px solid #e2e8f0; }
    table.details td.label { color: #64748b; width: 40%; }
    table.details td.value { font-weight: bold; text-align: right; }
    .amount-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-top: 20px; text-align: right; }
    .amount-label { color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
    .amount-value { font-size: 26px; font-weight: bold; color: #0F4C81; margin-top: 4px; }
    .footer { margin-top: 48px; color: #94a3b8; font-size: 10px; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 16px; }
</style>
</head>
<body>
    <div class="header">
        <table style="width:100%;"><tr>
            <td>{$logoHtml}<div class="muted">{$contactEmail} &nbsp; {$contactPhone}</div></td>
            <td class="header-right"><span class="stamp">PAID</span></td>
        </tr></table>
    </div>

    <h1>Payment Receipt</h1>
    <p class="muted">Issued to {$this->customerLine($order)}</p>

    <table class="details">{$rowsHtml}</table>

    <div class="amount-box">
        <div class="amount-label">Amount Paid</div>
        <div class="amount-value">{$currency} {$amount}</div>
    </div>

    <div class="footer">
        This receipt was generated automatically by {$siteName}. For questions about this payment, contact {$contactEmail}.
    </div>
</body>
</html>
HTML;
    }

    private function customerLine(array $order): string
    {
        $name = e((string) ($order['customer_name'] ?? ''));
        $email = e((string) ($order['customer_email'] ?? ''));

        return trim($name . ' &lt;' . $email . '&gt;');
    }

    private function methodLabel(array $payment): string
    {
        $gateway = (string) ($payment['gateway'] ?? '');
        $channel = (string) ($payment['channel'] ?? '');

        if ($gateway === 'paystack') {
            return $channel !== '' ? 'Paystack (' . ucfirst($channel) . ')' : 'Paystack';
        }

        if ($gateway === 'manual') {
            return 'Bank Transfer';
        }

        return ucfirst($gateway ?: 'Online Payment');
    }
}
