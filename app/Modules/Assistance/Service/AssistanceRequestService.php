<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Service;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\BaseService;
use App\Core\Config;
use App\Core\Logger;
use App\Core\Settings;
use App\Core\Helpers\Validator;
use App\Modules\Assistance\Repository\AssistanceRequestRepository;
use App\Modules\Cms\Repository\ServiceRepository;
use App\Modules\Growth\Service\GrowthAnalyticsService;
use PHPMailer\PHPMailer\PHPMailer;

final class AssistanceRequestService extends BaseService
{
    public function __construct(private readonly AssistanceRequestRepository $requests, private readonly AssistanceNotificationService $notifications, private readonly ServiceRepository $services, private readonly GrowthAnalyticsService $growth) {}

    public function submit(array $data, string $ip, string $userAgent): array
    {
        if (trim((string)($data['website'] ?? '')) !== '') return ['success' => true, 'id' => 0, 'reference' => ''];

        $validator = new Validator($data, [
            'name' => 'required|min:2|max:120',
            'phone' => 'required|min:9|max:20',
            'email' => 'email|max:190',
            'category' => 'required|in:government,business,documents,jobs,website,software,other',
            'message' => 'required|min:10|max:3000',
            'preferred_contact' => 'required|in:whatsapp,phone,email',
            'consent' => 'required|in:1',
        ]);
        if ($validator->fails()) return ['success' => false, 'errors' => $validator->errors()];

        $service = null;
        $intakeAnswers = [];
        if (!empty($data['service_id'])) {
            $service = $this->services->findByIdPublished((int)$data['service_id']);
            if (!$service) return ['success' => false, 'errors' => ['service_id' => ['Please choose a valid service.']]];
            $questions = json_decode((string)($service['intake_questions'] ?? '[]'), true);
            $submitted = is_array($data['intake_answers'] ?? null) ? $data['intake_answers'] : [];
            foreach (is_array($questions) ? $questions : [] as $question) {
                if (!is_array($question) || empty($question['key'])) continue;
                $key = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$question['key']);
                if ($key === '') continue;
                $value = trim((string)($submitted[$key] ?? ''));
                if (!empty($question['required']) && $value === '') {
                    return ['success' => false, 'errors' => ['intake_answers' => ['Please answer: ' . (string)($question['label'] ?? $key)]]];
                }
                if ($value !== '') $intakeAnswers[$key] = mb_substr($value, 0, 1000);
            }
        }

        $reference = $this->reference();
        $id = $this->requests->createRequest([
            'request_number' => $reference,
            'name' => trim((string)$data['name']),
            'phone' => trim((string)$data['phone']),
            'email' => trim((string)($data['email'] ?? '')) ?: null,
            'category' => trim((string)$data['category']),
            'service_id' => !empty($data['service_id']) ? (int)$data['service_id'] : null,
            'message' => trim((string)$data['message']),
            'intake_answers' => $intakeAnswers ? json_encode($intakeAnswers, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) : null,
            'preferred_contact' => trim((string)$data['preferred_contact']),
            'status' => 'new',
            'customer_user_id' => (Auth::check() && Auth::hasRole('customer') && !Auth::can('users.view')) ? Auth::id() : null,
            'ip_address' => $ip,
            'user_agent' => substr($userAgent, 0, 255),
            'consent_at' => date('Y-m-d H:i:s'),
        ]);

        $this->requests->addHistory($id, null, 'new', null, 'Request received from public assistance form.');
        AuditLog::record('assistance_request.received', 'assistance_request', $id);
        $request = $this->requests->findWithDetails($id) ?? ['id'=>$id,'request_number'=>$reference,'name'=>$data['name'],'email'=>$data['email'] ?? '','phone'=>$data['phone']];
        $this->notifications->requestReceived($request);
        $this->notifyAdmin($data, $reference);
        $this->growth->event('assistance_request_created', '/get-help', $service ? (int)$service['id'] : null, $id, ['category' => (string)$data['category']]);

        return ['success' => true, 'id' => $id, 'reference' => $reference];
    }

    private function reference(): string
    {
        return 'AT-HLP-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    private function notifyAdmin(array $data, string $reference): void
    {
        $recipient = Settings::get('contact_email');
        if (!$recipient || empty($_ENV['MAIL_HOST'] ?? null)) return;
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'];
            $mail->Port = (int)($_ENV['MAIL_PORT'] ?? 587);
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USER'] ?? '';
            $mail->Password = $_ENV['MAIL_PASS'] ?? '';
            $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
            $mail->setFrom($_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@' . parse_url(Config::get('app.url'), PHP_URL_HOST), $_ENV['MAIL_FROM_NAME'] ?? Settings::get('site_name', 'AlbaTech Solutions'));
            $mail->addAddress($recipient);
            if (!empty($data['email'])) $mail->addReplyTo((string)$data['email'], (string)$data['name']);
            $mail->Subject = 'New assistance request — ' . $reference;
            $mail->Body = sprintf("New AlbaTech assistance request\n\nReference: %s\nName: %s\nPhone: %s\nEmail: %s\nCategory: %s\nPreferred contact: %s\n\n%s", $reference, $data['name'], $data['phone'], $data['email'] ?? '—', $data['category'], $data['preferred_contact'], $data['message']);
            $mail->send();
        } catch (\Throwable $e) {
            Logger::warning('Assistance request email notification failed: ' . $e->getMessage());
        }
    }
}
