<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Safe server-side Git operations for deployment automation.
 *
 * Only status, fetch and fast-forward-only pull are exposed. The executable
 * is configurable, but arguments are fixed; callers cannot inject arbitrary
 * shell commands or git options.
 */
final class GitClient
{
    public function __construct(
        private readonly string $workingDirectory,
        private readonly string $gitBinary = 'git'
    ) {
    }

    /** @return array{branch:string,clean:bool,lines:list<string>} */
    public function status(): array
    {
        $branch = trim($this->run(['rev-parse', '--abbrev-ref', 'HEAD']));
        $status = $this->run(['status', '--porcelain']);

        $lines = $status === '' ? [] : preg_split('/\R/', trim($status)) ?: [];

        return [
            'branch' => $branch,
            'clean' => $lines === [],
            'lines' => $lines,
        ];
    }

    public function fetch(): void
    {
        $this->run(['fetch', '--prune', '--quiet', 'origin']);
    }

    /**
     * Fast-forward-only pull from the configured remote tracking branch.
     */
    public function pullFastForwardOnly(string $remote = 'origin', string $branch = 'main'): void
    {
        $this->run(['pull', '--ff-only', '--quiet', $remote, $branch]);
    }

    /** @param list<string> $arguments */
    private function run(array $arguments): string
    {
        $command = escapeshellcmd($this->gitBinary);
        foreach ($arguments as $argument) {
            $command .= ' ' . escapeshellarg($argument);
        }

        $descriptor = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptor, $pipes, $this->workingDirectory, null, ['bypass_shell' => true]);
        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start git process.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException('Git operation failed: ' . trim($stderr ?: $stdout));
        }

        return trim($stdout);
    }
}
