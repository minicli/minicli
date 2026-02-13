<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Commands;

use Assets\Fixtures\App\app\Middlewares\InvalidMiddleware;
use Minicli\Attributes\Command;
use Minicli\Attributes\Middleware;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Middleware([InvalidMiddleware::class])]
#[Command(name: 'middleware-invalid', description: 'Use invalid middleware class')]
final class MiddlewareInvalidCommand extends ConsoleCommand
{
    public function __invoke(): ExitCode
    {
        return ExitCode::Success;
    }
}
