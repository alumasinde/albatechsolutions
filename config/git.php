<?php

declare(strict_types=1);

return [
    'auto_pull' => filter_var($_ENV['GIT_AUTO_PULL'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'github_repo' => trim((string) ($_ENV['GIT_GITHUB_REPO'] ?? '')),
    'deploy_branch' => trim((string) ($_ENV['GIT_DEPLOY_BRANCH'] ?? 'main')),
];
