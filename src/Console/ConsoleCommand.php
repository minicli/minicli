<?php

declare(strict_types=1);

namespace Minicli\Console;

use Minicli\App;
use Minicli\Contracts\CommandInterface;
use Minicli\Log\Logger;

abstract class ConsoleCommand implements CommandInterface
{
    protected App $app;

    protected Logger $logger;

    /**
     * Called after the command execution
     */
    public function teardown(): void {}

    /**
     * Called before the command execution
     */
    public function boot(App $app): void
    {
        $this->app = $app;
        $this->logger = $app->logger;
    }

    protected function config(string $name): mixed
    {
        return $this->app->config($name);
    }
}
