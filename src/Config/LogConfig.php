<?php

declare(strict_types=1);

namespace Minicli\Config;

use Minicli\Attributes\Config;
use Minicli\Log\LogLevel;
use Minicli\Log\LogType;

#[Config('log')]
final readonly class LogConfig
{
    public function __construct(
        public LogType $type = LogType::SINGLE,
        public LogLevel $level = LogLevel::INFO,
        public string $timestampFormat = 'Y-m-d H:i:s',
    ) {}
}
