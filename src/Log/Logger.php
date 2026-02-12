<?php

declare(strict_types=1);

namespace Minicli\Log;

use Minicli\App;
use Minicli\Config\LogConfig;
use Minicli\Contracts\ServiceInterface;
use Minicli\Exceptions\BindingResolutionException;
use ReflectionException;
use RuntimeException;

class Logger implements ServiceInterface
{
    private const string DEFAULT_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

    private string $logsPath;

    private LogType $logType;

    private LogLevel $logLevel;

    private string $timestampFormat;

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function load(App $app): void
    {
        /** @var LogConfig $config */
        $config = $app->config('log');

        $this->logsPath = $app->logsPath();
        $this->logType = $config->type ?? LogType::SINGLE;
        $this->logLevel = $config->level ?? LogLevel::INFO;
        $this->timestampFormat = $config->timestampFormat ?? self::DEFAULT_TIMESTAMP_FORMAT;
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
        if (! is_dir($this->logsPath) && (! mkdir($this->logsPath, 0775, true) && ! is_dir($this->logsPath))) {
            throw new RuntimeException("Unable to create logs directory: {$this->logsPath}");
        }

        $logFile = $this->getLogFilePath();

        if (! file_exists($logFile) && ! touch($logFile)) {
            throw new RuntimeException("Unable to create log file: {$logFile}");
        }

        if (file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException("Unable to write log file: {$logFile}");
        }
    }

    private function getLogFilePath(): string
    {
        return match ($this->logType) {
            LogType::DAILY => sprintf("{$this->logsPath}/minicli-%s.log", date('Y-m-d')),
            default => "{$this->logsPath}/minicli.log",
        };
    }
}
