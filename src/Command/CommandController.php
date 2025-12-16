<?php

declare(strict_types=1);

namespace Minicli\Command;

use BadMethodCallException;
use Deprecated;
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
     * @param  App  $app
     */
    protected App $app;

    /**
     * config instance.
     *
     * @param  Config  $config
     */
    protected Config $config;

    /**
     * logger instance.
     *
     * @param  Logger  $logger
     */
    protected Logger $logger;

    /**
     * command call instance.
     *
     * @param  CommandCall  $input
     */
    protected CommandCall $input;

    /**
     * output handler instance.
     *
     * @param  OutputHandler  $printer
     */
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
     * Called when `run` is successfully finished.
     */
    public function teardown(): void {}

    /**
     * handle command.
     */
    abstract public function handle(): void;

    /**
     * Called before `run`
     *
     * @throws MissingParametersException
     */
    public function boot(App $app, CommandCall $input): void
    {
        $this->app = $app;
        $this->config = $app->config;
        $this->logger = $app->logger;
        $this->printer = $app->getPrinter();

        $missing = array_diff($this->required(), array_keys($input->params));

        if ($missing !== []) {
            throw new MissingParametersException($missing);
        }
    }

    /**
     * run command
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
     */
    protected function hasParam(string $param): bool
    {
        return $this->input->hasParam($param);
    }

    /**
     * check has flag
     */
    protected function hasFlag(string $flag): bool
    {
        return $this->input->hasFlag($flag);
    }

    /**
     * get parameter
     */
    protected function getParam(string $param): ?string
    {
        return $this->input->getParam($param);
    }

    /**
     * get app instance
     */
    protected function getApp(): App
    {
        return $this->app;
    }

    /**
     * get output handler instance
     */
    #[Deprecated]
    protected function getPrinter(): OutputHandler
    {
        return $this->printer;
    }
}
