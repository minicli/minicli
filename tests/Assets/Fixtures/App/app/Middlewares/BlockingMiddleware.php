<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Middlewares;

use Closure;
use Minicli\App;
use Minicli\Console\CommandCall;
use Minicli\Console\ExitCode;
use Minicli\Contracts\MiddlewareInterface;

final class BlockingMiddleware implements MiddlewareInterface
{
    /**
     * @param  Closure(CommandCall, App): ExitCode  $next
     */
    public function handle(CommandCall $input, App $app, Closure $next): ExitCode
    {
        echo 'blocked';

        return ExitCode::Invalid;
    }
}
