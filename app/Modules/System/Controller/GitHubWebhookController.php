<?php

declare(strict_types=1);

namespace App\Modules\System\Controller;

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

        $payload = $request->rawBody();
        $signature = trim((string) ($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? ''));
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        if ($payload === '' || $signature === '' || !hash_equals($expected, $signature)) {
            Logger::security('GitHub webhook signature verification failed.', ['ip' => $request->ip()]);
            return Response::text('Invalid signature.', 401, ['Cache-Control' => 'no-store']);
        }

        $event = trim((string) ($_SERVER['HTTP_X_GITHUB_EVENT'] ?? ''));
        $data = json_decode($payload, true);
        if (!is_array($data)) {
            return Response::text('Invalid payload.', 400, ['Cache-Control' => 'no-store']);
        }

        $configuredRepo = trim((string) ($_ENV['GIT_GITHUB_REPO'] ?? ''));
        $targetBranch = trim((string) ($_ENV['GIT_DEPLOY_BRANCH'] ?? 'main'));
        $repository = trim((string) ($data['repository']['full_name'] ?? ''));
        if ($configuredRepo === '' || $repository !== $configuredRepo) {
            Logger::security('GitHub webhook repository mismatch.', ['repo' => $repository]);
            return Response::text('Invalid webhook repository.', 403, ['Cache-Control' => 'no-store']);
        }

        if ($event === 'ping') {
            return Response::json(['ok' => true, 'event' => 'ping']);
        }

        if ($event !== 'push') {
            return Response::json(['ok' => true, 'ignored' => true], 202);
        }

        $ref = (string) ($data['ref'] ?? '');
        if ($ref !== 'refs/heads/' . $targetBranch) {
            return Response::json(['ok' => true, 'ignored' => true, 'reason' => 'branch'], 202);
        }

        if (!filter_var($_ENV['GIT_AUTO_PULL'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return Response::json(['ok' => true, 'pulled' => false], 202);
        }

        // Four levels above Controller reaches the repository root.
        $workingDirectory = dirname(__DIR__, 4);
        $git = new GitClient($workingDirectory);
        $status = $git->status();
        if (!$status['clean']) {
            Logger::security('GitHub auto-pull skipped: working tree is not clean.', ['branch' => $status['branch']]);
            return Response::json(['ok' => false, 'pulled' => false, 'reason' => 'working_tree_not_clean'], 409);
        }

        $git->fetch();
        $git->pullFastForwardOnly('origin', $targetBranch);
        Logger::info('GitHub auto-pull completed.', ['repo' => $repository, 'branch' => $targetBranch]);

        return Response::json(['ok' => true, 'pulled' => true]);
    }
}
