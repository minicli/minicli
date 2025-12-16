<?php

declare(strict_types=1);

namespace Minicli\Console;

use BadMethodCallException;
use Minicli\App;
use Minicli\Contracts\ControllerInterface;
use Minicli\Log\Logger;
use Minicli\Output\OutputHandler;

/**
 * @mixin OutputHandler
 */
abstract class CommandController implements ControllerInterface
{
    protected App $app;

    protected Logger $logger;

    protected CommandCall $input;

    private OutputHandler $printer;

    /**
     * @param  array<int,mixed>  $arguments
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
    public function boot(App $app, CommandCall $input): void
    {
        $this->app = $app;
        $this->logger = $app->logger;
        $this->printer = $app->printer;
    }

    /**
     * @return array<int, string>
     */
    protected function getArgs(): array
    {
        return $this->input->args;
    }

    /**
     * @return array<string, string>
     */
    protected function getParams(): array
    {
        return $this->input->params;
    }

    protected function hasParam(string $param): bool
    {
        return $this->input->hasParam($param);
    }

    protected function hasFlag(string $flag): bool
    {
        return $this->input->hasFlag($flag);
    }

    protected function getParam(string $param): ?string
    {
        return $this->input->getParam($param);
    }
}
