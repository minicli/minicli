<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Commands;

use Minicli\Attributes\Command;
use Minicli\Attributes\DefaultCommand;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Command(name: 'default-priority', description: 'Command with default method priority')]
final class DefaultMethodPriorityCommand extends ConsoleCommand
{
    public function default(): ExitCode
    {
        echo 'method-default';

        return ExitCode::Success;
    }

    #[DefaultCommand]
    #[Command(description: 'Default attribute candidate')]
    public function candidate(): ExitCode
    {
        echo 'attribute-default';

        return ExitCode::Success;
    }
}
