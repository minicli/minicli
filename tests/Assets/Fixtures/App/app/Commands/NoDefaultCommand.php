<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Commands;

use Minicli\Attributes\Command;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Command(name: 'no-default', description: 'Command without default subcommand')]
final class NoDefaultCommand extends ConsoleCommand
{
    #[Command(description: 'Alpha option')]
    public function alpha(): ExitCode
    {
        echo 'alpha';

        return ExitCode::Success;
    }

    #[Command(description: 'Beta option')]
    public function beta(): ExitCode
    {
        echo 'beta';

        return ExitCode::Success;
    }
}
