<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\app\Middlewares;

use Closure;
use Minicli\App;
use Minicli\Console\CommandCall;
use Minicli\Console\ExitCode;
use Minicli\Contracts\MiddlewareInterface;

final readonly class ConfiguredMiddleware implements MiddlewareInterface
{
    public function __construct(
        private int $tries = 1,
        private int $sleep = 0,
    ) {}

    /**
     * @param  Closure(CommandCall, App): ExitCode  $next
     */
    public function handle(CommandCall $input, App $app, Closure $next): ExitCode
    {
        echo "retry({$this->tries},{$this->sleep})>";
        $result = $next($input, $app);
        echo '<retry';

        return $result;
    }
}
