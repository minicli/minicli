<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Commands;

use Minicli\Attributes\Command;
use Minicli\Attributes\DefaultCommand;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Command(name: 'default-attribute', description: 'Command with default attribute')]
final class DefaultAttributeCommand extends ConsoleCommand
{
    #[Command(description: 'First option')]
    public function first(): ExitCode
    {
        echo 'first';

        return ExitCode::Success;
    }

    #[DefaultCommand]
    #[Command(description: 'Second option')]
    public function second(): ExitCode
    {
        echo 'second';

        return ExitCode::Success;
    }
}
