<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Service;

use App\Core\Settings;
use App\Modules\Assistance\Repository\AssistanceQuoteRepository;
use Dompdf\Dompdf;
use Dompdf\Options;

final class AssistanceReceiptService
{
    public function __construct(private readonly AssistanceQuoteRepository $quotes) {}

    public function renderForToken(string $token): ?string
    {
        $payment = $this->quotes->findPaymentByReceiptToken($token);
        if (!$payment) return null;
        $items = $this->quotes->items((int)$payment['quote_id']);

        $dompdf = new Dompdf($this->options());
        $dompdf->loadHtml($this->html($payment, $items));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }

    private function options(): Options
    {
        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        return $options;
    }

    private function html(array $payment, array $items): string
    {
        $site = e(Settings::get('site_name', 'AlbaTech Solutions'));
        $email = e(Settings::get('contact_email', ''));
        $phone = e(Settings::get('contact_phone', ''));
        $paidAt = $payment['verified_at'] ?? $payment['updated_at'] ?? date('Y-m-d H:i:s');
        $rows = '';
        foreach ($items as $item) {
            $rows .= '<tr><td>'.e((string)$item['description']).'</td><td class="num">'.e(number_format((float)$item['quantity'],2)).'</td><td class="num">KES '.e(number_format((float)$item['unit_price'],2)).'</td><td class="num">KES '.e(number_format((float)$item['line_total'],2)).'</td></tr>';
        }
        $amount = number_format((float)$payment['amount'],2);
        $customer = e((string)$payment['name']);
        $ref = e((string)$payment['mpesa_receipt']);
        $quote = e((string)$payment['quote_number']);
        $date = e(date('d M Y, H:i', strtotime((string)$paidAt)));
        return <<<HTML
<!doctype html><html><head><meta charset="utf-8"><style>
body{font-family:Helvetica,sans-serif;color:#14213d;font-size:12px;margin:36px}.top{border-bottom:3px solid #0f766e;padding-bottom:18px;margin-bottom:28px}.brand{font-size:22px;font-weight:bold}.muted{color:#64748b}.paid{float:right;border:2px solid #15803d;color:#15803d;padding:7px 14px;font-weight:bold;letter-spacing:1px}h1{font-size:22px;margin:0 0 6px}table{width:100%;border-collapse:collapse}.meta td{padding:7px 0;border-bottom:1px solid #e5e7eb}.meta td:first-child{color:#64748b;width:38%}.items{margin-top:26px}.items th,.items td{padding:10px 8px;border-bottom:1px solid #e5e7eb;text-align:left}.items th{background:#f8fafc}.num{text-align:right!important}.total{margin-top:20px;text-align:right;font-size:20px;font-weight:bold}.footer{margin-top:50px;border-top:1px solid #e5e7eb;padding-top:14px;color:#64748b;font-size:10px;text-align:center}.notice{margin-top:18px;padding:12px;background:#f8fafc;border:1px solid #e5e7eb}
</style></head><body>
<div class="top"><span class="paid">PAID</span><div class="brand">{$site}</div><div class="muted">{$email} &nbsp; {$phone}</div></div>
<h1>Payment Receipt</h1><p class="muted">Issued to {$customer}</p>
<table class="meta"><tr><td>Receipt reference</td><td>AT-RCPT-{$payment['id']}</td></tr><tr><td>Quote</td><td>{$quote}</td></tr><tr><td>M-Pesa receipt</td><td>{$ref}</td></tr><tr><td>Payment date</td><td>{$date}</td></tr><tr><td>Payment method</td><td>M-Pesa</td></tr></table>
<table class="items"><thead><tr><th>Description</th><th class="num">Qty</th><th class="num">Unit</th><th class="num">Total</th></tr></thead><tbody>{$rows}</tbody></table>
<div class="total">KES {$amount}</div>
<div class="notice">This receipt confirms that AlbaTech Solutions verified the payment reference above. It is not a government receipt and does not replace any official government payment receipt.</div>
<div class="footer">{$site} · Helping Kenyans get things done digitally.</div>
</body></html>
HTML;
    }
}
