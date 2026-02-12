<?php

declare(strict_types=1);

use Minicli\Attributes\Config;
use Minicli\Log\LogLevel;
use Minicli\Log\LogType;

#[Config('log')]
final readonly class TestLogConfig
{
    public function __construct(
        public LogType $type = LogType::SINGLE,
        public LogLevel $level = LogLevel::INFO,
        public string $timestampFormat = 'Y-m-d H:i:s',
    ) {}
}
