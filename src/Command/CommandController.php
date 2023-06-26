<?php

declare(strict_types=1);

namespace Minicli\Command;

use BadMethodCallException;
use Minicli\App;
use Minicli\Config;
use Minicli\ControllerInterface;
use Minicli\Exception\MissingParametersException;
use Minicli\Logging\Logger;
use Minicli\Output\OutputHandler;

/**
 * @mixin OutputHandler
 */
abstract class CommandController implements ControllerInterface
{
    /**
     * app instance.
     *
     * @param App $app
     */
    protected App $app;

    /**
     * config instance.
     *
     * @param Config $config
     */
    protected Config $config;

    /**
     * logger instance.
     *
     * @param Logger $logger
     */
    protected Logger $logger;

    /**
     * command call instance.
     *
     * @param CommandCall $input
     */
    protected CommandCall $input;

    /**
     * output handler instance.
     *
     * @param OutputHandler $printer
     */
    private OutputHandler $printer;

    /**
     * handle command.
     *
     * @return void
     */
    abstract public function handle(): void;

    /**
     * Called before `run`
     *
     * @param App $app
     * @param CommandCall $input
     * @return void
     * @throws MissingParametersException
     */
    public function boot(App $app, CommandCall $input): void
    {
        $this->app = $app;
        $this->config = $app->config;
        $this->logger = $app->logger;
        $this->printer = $app->getPrinter();

        $missing = array_diff($this->required(), array_keys($input->params));

        if ([] !== $missing) {
            throw new MissingParametersException($missing);
        }
    }

    /**
     * run command
     * @param CommandCall $input
     * @return void
     */
    public function run(CommandCall $input): void
    {
        $this->input = $input;
        $this->handle();
    }

    /**
     * The list of parameters required by the command.
     *
     * @return array<int, string>
     */
    public function required(): array
    {
        return [];
    }

    /**
     * Called when `run` is successfully finished.
     *
     * @return void
     */
    public function teardown(): void
    {
    }

    /**
     * get arguments
     *
     * @return array<int, string>
     */
    protected function getArgs(): array
    {
        return $this->input->args;
    }

    /**
     * get parameters
     *
     * @return array<string, string>
     */
    protected function getParams(): array
    {
        return $this->input->params;
    }

    /**
     * check has parameter
     *
     * @param string $param
     * @return bool
     */
    protected function hasParam(string $param): bool
    {
        return $this->input->hasParam($param);
    }

    /**
     * check has flag
     *
     * @param string $flag
     * @return bool
     */
    protected function hasFlag(string $flag): bool
    {
        return $this->input->hasFlag($flag);
    }

    /**
     * get parameter
     *
     * @param string $param
     * @return string|null
     */
    protected function getParam(string $param): ?string
    {
        return $this->input->getParam($param);
    }

    /**
     * get app instance
     *
     * @return App
     */
    protected function getApp(): App
    {
        return $this->app;
    }

    /**
     * get output handler instance
     *
     * @return OutputHandler
     * @deprecated
     */
    protected function getPrinter(): OutputHandler
    {
        return $this->printer;
    }

    /**
     * @param string $name
     * @param array<int,mixed> $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this->printer, $name)) {
            return $this->printer->$name(...$arguments);
        }

        throw new BadMethodCallException("Method {$name} does not exist.");
    }
}
