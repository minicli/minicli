<?php

declare(strict_types=1);

namespace Minicli\Logging;

use Minicli\App;
use Minicli\ServiceInterface;

class Logger implements ServiceInterface
{
    private const DEFAULT_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

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
     * @param string $message
     * @param array<mixed> $context
     * @param LogLevel|null $level
     * @return void
     */
    public function log(string $message, array $context = [], ?LogLevel $level = null): void
    {
        $level ??= $this->logLevel;

        $this->writeLog(sprintf(
            "[%s] %s: %s%s\n",
            date($this->timestampFormat),
            $level->value,
            $message,
            [] === $context ? '' : ' - '.json_encode($context)
        ));
    }

    /**
     * @param string $message
     * @param array<mixed> $context
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log($message, $context, LogLevel::INFO);
    }

    /**
     * @param string $message
     * @param array<mixed> $context
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log($message, $context, LogLevel::WARNING);
    }

    /**
     * @param string $message
     * @param array<mixed> $context
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log($message, $context, LogLevel::ERROR);
    }

    /**
     * @param string $message
     * @param array<mixed> $context
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log($message, $context, LogLevel::DEBUG);
    }

    private function writeLog(string $message): void
    {
        if ( ! is_dir($this->logsPath)) {
            mkdir($this->logsPath, 0775, true);
        }

        $logFile = $this->getLogFilePath();

        if ( ! file_exists($logFile)) {
            touch($logFile);
        }

        file_put_contents($logFile, $message, FILE_APPEND);
    }

    private function getLogFilePath(): string
    {
        return match ($this->logType) {
            LogType::DAILY => sprintf("{$this->logsPath}/minicli-%s.log", date('Y-m-d')),
            default => "{$this->logsPath}/minicli.log",
        };
    }
}
