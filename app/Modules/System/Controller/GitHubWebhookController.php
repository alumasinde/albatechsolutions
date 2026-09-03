<?php

declare(strict_types=1);

namespace App\Modules\System\Controller;

use App\Core\Config;
use App\Core\GitClient;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;

final class GitHubWebhookController
{
    public function __invoke(Request $request): Response
    {
        $secret = trim((string) ($_ENV['GIT_WEBHOOK_SECRET'] ?? ''));
        if ($secret === '') {
            Logger::security('GitHub webhook rejected: webhook secret not configured.');
            return Response::text('Webhook unavailable.', 503, ['Cache-Control' => 'no-store']);
        }

        $payload = file_get_contents('php://input');
        if ($payload === false) {
            return Response::text('Invalid payload.', 400, ['Cache-Control' => 'no-store']);
        }

        $signature = trim((string) ($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? ''));
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        if ($signature === '' || !hash_equals($expected, $signature)) {
            Logger::security('GitHub webhook signature verification failed.', ['ip' => $request->ip()]);
            return Response::text('Invalid signature.', 401, ['Cache-Control' => 'no-store']);
        }

        $event = trim((string) ($_SERVER['HTTP_X_GITHUB_EVENT'] ?? ''));
        if ($event === 'ping') {
            return Response::json(['ok' => true, 'event' => 'ping'], 200);
        }

        if ($event !== 'push') {
            return Response::json(['ok' => true, 'ignored' => true], 202);
        }

        $repo = trim((string) ($_ENV['GIT_GITHUB_REPO'] ?? ''));
        $targetBranch = trim((string) ($_ENV['GIT_DEPLOY_BRANCH'] ?? 'main'));
        $autoPull = filter_var($_ENV['GIT_AUTO_PULL'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $payloadData = json_decode($payload, true);

        if ($repo === '' || !is_array($payloadData)) {
            Logger::warning('GitHub push webhook rejected: repository configuration or payload invalid.');
            return Response::text('Invalid webhook configuration.', 400, ['Cache-Control' => 'no-store']);
        }

        $ref = (string) ($payloadData['ref'] ?? '');
        if ($ref !== 'refs/heads/' . $targetBranch) {
            return Response::json(['ok' => true, 'ignored' => true, 'reason' => 'branch'], 202);
        }

        if (!$autoPull) {
            return Response::json(['ok' => true, 'pulled' => false], 202);
        }

        $workingDirectory = dirname(__DIR__, 3);
        $git = new GitClient($workingDirectory);
        $status = $git->status();
        if (!$status['clean']) {
            Logger::security('GitHub auto-pull skipped: working tree is not clean.', ['branch' => $status['branch']]);
            return Response::json(['ok' => false, 'pulled' => false, 'reason' => 'working_tree_not_clean'], 409);
        }

        $git->fetch();
        $git->pullFastForwardOnly('origin', $targetBranch);

        Logger::info('GitHub auto-pull completed.', ['repo' => $repo, 'branch' => $targetBranch]);

        return Response::json(['ok' => true, 'pulled' => true], 200);
    }
}
