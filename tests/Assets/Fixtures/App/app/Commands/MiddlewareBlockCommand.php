<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Commands;

use Assets\Fixtures\App\app\Middlewares\BlockingMiddleware;
use Minicli\Attributes\Command;
use Minicli\Attributes\Middleware;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Middleware([BlockingMiddleware::class])]
#[Command(name: 'middleware-block', description: 'Block command execution')]
final class MiddlewareBlockCommand extends ConsoleCommand
{
    public function __invoke(): ExitCode
    {
        echo 'should-not-run';

        return ExitCode::Success;
    }
}
