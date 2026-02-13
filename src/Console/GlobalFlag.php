<?php

declare(strict_types=1);

namespace Minicli\Console;

enum GlobalFlag: string
{
    case HELP = '--help';
    case QUIET = '--quiet';
    case PROFILE = '--profile';

    public function description(): string
    {
        return match ($this) {
            self::HELP => 'Display command help information.',
            self::QUIET => 'Suppress command output messages.',
            self::PROFILE => 'Show command execution timing and memory stats.',
        };
    }
}
