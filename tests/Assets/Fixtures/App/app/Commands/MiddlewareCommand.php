<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Commands;

use Assets\Fixtures\App\app\Middlewares\PrefixMiddleware;
use Assets\Fixtures\App\app\Middlewares\SuffixMiddleware;
use Minicli\Attributes\Command;
use Minicli\Attributes\Middleware;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Middleware([PrefixMiddleware::class, SuffixMiddleware::class])]
#[Command(name: 'middleware-test', description: 'Run command middleware pipeline')]
final class MiddlewareCommand extends ConsoleCommand
{
    public function __invoke(): ExitCode
    {
        echo 'command';

        return ExitCode::Success;
    }
}
