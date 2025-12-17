<?php

declare(strict_types=1);

namespace Minicli\Console;

use BadMethodCallException;
use Exception;
use Minicli\App;
use Minicli\Contracts\CommandInterface;
use Minicli\Log\Logger;
use Minicli\Output\OutputHandler;

/**
 * @mixin OutputHandler
 */
abstract class ConsoleCommand implements CommandInterface
{
    protected App $app;

    protected Logger $logger;

    private OutputHandler $printer;

    /**
     * @param  array<mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this->printer, $name)) {
            return $this->printer->{$name}(...$arguments);
        }

        throw new BadMethodCallException("Method {$name} does not exist.");
    }

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
        $this->printer = $app->printer;
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
