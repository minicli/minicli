<?php

declare(strict_types=1);

namespace Minicli\Command;

use Minicli\App;
use Minicli\ControllerInterface;
use Minicli\Output\OutputHandler;

abstract class CommandController implements ControllerInterface
{
    /**
     * app instance.
     *
     * @param App $app
     */
    protected App $app;

    /**
     * command call instance.
     *
     * @param CommandCall $input
     */
    protected CommandCall $input;

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
     * @return void
     */
    public function boot(App $app): void
    {
        $this->app = $app;
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
     * @return array
     */
    protected function getArgs(): array
    {
        return $this->input->args;
    }

    /**
     * get parameters
     *
     * @return array
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
     */
    protected function getPrinter(): OutputHandler
    {
        return $this->getApp()->getPrinter();
    }
}
