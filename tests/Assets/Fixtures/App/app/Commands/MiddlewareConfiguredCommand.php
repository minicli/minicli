<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Commands;

use Assets\Fixtures\App\app\Middlewares\ConfiguredMiddleware;
use Minicli\Attributes\Command;
use Minicli\Attributes\Middleware;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Middleware([ConfiguredMiddleware::class => ['tries' => 3, 'sleep' => 2000]])]
#[Command(name: 'middleware-configured', description: 'Run middleware with custom configuration')]
final class MiddlewareConfiguredCommand extends ConsoleCommand
{
    public function __invoke(): ExitCode
    {
        echo 'command';

        return ExitCode::Success;
    }
}
