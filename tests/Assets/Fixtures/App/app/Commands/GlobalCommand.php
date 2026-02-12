<?php

declare(strict_types=1);

use Minicli\Attributes\Command;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Command(name: 'global-cmd', description: 'Global namespace command')]
final class GlobalCommand extends ConsoleCommand
{
    public function __invoke(): ExitCode
    {
        return ExitCode::Success;
    }
}
