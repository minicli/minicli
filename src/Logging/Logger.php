<?php

declare(strict_types=1);

namespace Minicli\Logging;

use Minicli\App;
use Minicli\ServiceInterface;

class Logger implements ServiceInterface
{
    private const string DEFAULT_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

    private string $logsPath;

    private LogType $logType;

    private LogLevel $logLevel;

    private string $timestampFormat;

    public function load(App $app): void
    {
        $config = $app->config;

        $this->logsPath = $app->logs_path;
        $this->logType = LogType::from($config->logging['type'] ?? LogType::SINGLE->value);
        $this->logLevel = LogLevel::from($config->logging['level'] ?? LogLevel::INFO->value);
        $this->timestampFormat = $config->logging['timestamp_format'] ?? self::DEFAULT_TIMESTAMP_FORMAT;
    }

    /**
     * @param  array<mixed>  $context
     */
    public function log(string $message, array $context = [], ?LogLevel $level = null): void
    {
        $level ??= $this->logLevel;

        $this->writeLog(sprintf(
            "[%s] %s: %s%s\n",
            date($this->timestampFormat),
            $level->value,
            $message,
            $context === [] ? '' : ' - ' . json_encode($context)
        ));
    }

    /**
     * @param  array<mixed>  $context
     */
    public function info(string $message, array $context = []): void
    {
        $this->log($message, $context, LogLevel::INFO);
    }

    /**
     * @param  array<mixed>  $context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log($message, $context, LogLevel::WARNING);
    }

    /**
     * @param  array<mixed>  $context
     */
    public function error(string $message, array $context = []): void
    {
        $this->log($message, $context, LogLevel::ERROR);
    }

    /**
     * @param  array<mixed>  $context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log($message, $context, LogLevel::DEBUG);
    }

    private function writeLog(string $message): void
    {
        if (! is_dir($this->logsPath)) {
            mkdir($this->logsPath, 0775, true);
        }

        $logFile = $this->getLogFilePath();

        if (! file_exists($logFile)) {
            touch($logFile);
        }

        file_put_contents($logFile, $message, FILE_APPEND);
    }

    private function getLogFilePath(): string
    {
        return match ($this->logType) {
            LogType::DAILY => sprintf("{$this->logsPath}/minicli-%s.log", \Carbon\Carbon::now()->format('Y-m-d')),
            default => "{$this->logsPath}/minicli.log",
        };
    }
}
