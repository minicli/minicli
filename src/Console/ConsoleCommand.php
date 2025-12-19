<?php

declare(strict_types=1);

namespace Minicli\Console;

use Exception;
use Minicli\App;
use Minicli\Contracts\CommandInterface;
use Minicli\Log\Logger;

abstract class ConsoleCommand implements CommandInterface
{
    protected App $app;

    protected Logger $logger;

    protected bool $quiet = false;

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

    public function setQuiet(bool $quiet): void
    {
        $this->quiet = $quiet;
    }

    protected function config(string $name): mixed
    {
        try {
            return $this->app->config($name);
        } catch (Exception) {
            return null;
        }
    }
}
