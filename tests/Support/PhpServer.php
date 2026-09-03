<?php

declare(strict_types=1);

namespace WPROG2\Tests\Support;

use RuntimeException;
use Symfony\Component\Process\Process;

final class PhpServer
{
    private ?Process $process = null;
    private ?string $baseUrl = null;

    /** @param array<string, string> $environment */
    public function start(string $documentRoot, array $environment = []): string
    {
        if ($this->process !== null) {
            throw new RuntimeException('PHP server is already running.');
        }
        $port = self::availablePort();
        $address = "127.0.0.1:{$port}";
        $this->process = new Process(
            [PHP_BINARY, '-S', $address, '-t', $documentRoot],
            WPROG2_ROOT,
            $environment,
        );
        $this->process->start();
        $deadline = microtime(true) + 5.0;
        do {
            if (!$this->process->isRunning()) {
                throw new RuntimeException('PHP server stopped during startup: ' . $this->logs());
            }
            $socket = @fsockopen('127.0.0.1', $port, $errorCode, $errorMessage, 0.1);
            if (is_resource($socket)) {
                fclose($socket);
                $this->baseUrl = "http://{$address}/";
                return $this->baseUrl;
            }
            usleep(50_000);
        } while (microtime(true) < $deadline);

        $this->stop();
        throw new RuntimeException("PHP server did not start on {$address}.");
    }

    public function stop(): void
    {
        if ($this->process !== null) {
            $this->process->stop(1.0);
            $this->process = null;
            $this->baseUrl = null;
        }
    }

    public function logs(): string
    {
        return $this->process === null ? '' : trim($this->process->getOutput() . "\n" . $this->process->getErrorOutput());
    }

    public function __destruct()
    {
        $this->stop();
    }

    private static function availablePort(): int
    {
        $socket = stream_socket_server('tcp://127.0.0.1:0', $errorCode, $errorMessage);
        if ($socket === false) {
            throw new RuntimeException("Unable to allocate a test port: {$errorMessage} ({$errorCode})");
        }
        $address = stream_socket_get_name($socket, false);
        fclose($socket);
        if ($address === false || !str_contains($address, ':')) {
            throw new RuntimeException('Unable to determine allocated test port.');
        }
        return (int) substr(strrchr($address, ':'), 1);
    }
}
