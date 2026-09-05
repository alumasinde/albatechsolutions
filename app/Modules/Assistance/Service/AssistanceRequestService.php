<?php

declare(strict_types=1);

namespace App\Modules\Assistance\Service;

use App\Core\AuditLog;
use App\Core\Auth;
use App\Core\BaseService;
use App\Core\Helpers\Validator;
use App\Modules\Assistance\Repository\AssistanceRequestRepository;
use App\Modules\Cms\Repository\ServiceRepository;

final class AssistanceRequestService extends BaseService
{
    public function __construct(private readonly AssistanceRequestRepository $requests, private readonly AssistanceNotificationService $notifications, private readonly ServiceRepository $services) {}

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
            'ip_address' => $ip,
            'user_agent' => substr($userAgent, 0, 255),
            'consent_at' => date('Y-m-d H:i:s'),
        ]);

        $this->requests->addHistory($id, null, 'new', null, 'Request received from public assistance form.');
        AuditLog::record('assistance_request.received', 'assistance_request', $id);
        $request = $this->requests->findWithDetails($id) ?? ['id'=>$id,'request_number'=>$reference,'name'=>$data['name'],'email'=>$data['email'] ?? '','phone'=>$data['phone']];
        $this->notifications->requestReceived($request);
        $this->notifications->adminNewRequest($request);

        return ['success' => true, 'id' => $id, 'reference' => $reference];
    }

    private function reference(): string
    {
        return 'AT-HLP-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }


