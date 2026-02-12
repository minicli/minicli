<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Commands;

use Minicli\Attributes\Argument;
use Minicli\Attributes\Command;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Command(name: 'test', description: 'Test command root')]
final class TestCommand extends ConsoleCommand
{
    #[Command(name: 'greet', description: 'Greet by name', default: true)]
    public function greet(
        #[Argument(description: 'Target name')]
        string $name = 'world',
        bool $shout = false,
    ): ExitCode {
        $message = "Hello {$name}";

        echo $shout ? strtoupper($message) : $message;

        return ExitCode::Success;
    }

    #[Command(description: 'Count provided tags')]
    public function tags(array $tags = []): ExitCode
    {
        echo (string) count($tags);

        return ExitCode::Success;
    }

    #[Command(description: 'Require integer value')]
    public function cast(int $count): ExitCode
    {
        echo (string) $count;

        return ExitCode::Success;
    }
}
