<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Closure;
use Minicli\App;
use Minicli\Console\CommandCall;
use Minicli\Console\ExitCode;

interface MiddlewareInterface
{
    /**
     * @param  Closure(CommandCall, App): ExitCode  $next
     */
    public function handle(CommandCall $input, App $app, Closure $next): ExitCode;
}
