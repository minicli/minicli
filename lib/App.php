<?php

namespace Minicli;

use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;
use Minicli\Output\BasicPrinter;
use Minicli\Output\CliPrinter;

class App
{
    /** @var OutputInterface  */
    protected $printer;

    /** @var CommandRegistry  */
    protected $command_registry;

    /** @var  string  */
    protected $app_signature;

    public function __construct($app_path = null, OutputInterface $output = null)
    {
        if ($app_path === null) {
            $app_path = __DIR__ . '/../app/Command';
        }

        if ($output === null) {
            $this->printer = new BasicPrinter();
        } else {
            $this->printer = $output;
        }

        $this->command_registry = new CommandRegistry($app_path);
    }

    /**
     * @return OutputInterface
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->app_signature;
    }

    /**
     * @return void
     */
    public function printSignature()
    {
        $this->getPrinter()->display(sprintf("usage: %s", $this->getSignature()));
    }
    /**
     * @param string $app_signature
     */
    public function setSignature($app_signature)
    {
        $this->app_signature = $app_signature;
    }

    /**
     * @param string $name
     * @param callable $callable
     */
    public function registerCommand($name, $callable)
    {
        $this->command_registry->registerCommand($name, $callable);
    }

    /**
     * @param array $argv
     */
    public function runCommand(array $argv = [])
    {
        $input = new CommandCall($argv);

        if (count($input->args) < 2) {
            $this->printSignature();
            exit;
        }

        $controller = $this->command_registry->getCallableController($input->command, $input->subcommand);

        if ($controller instanceof ControllerInterface) {
            $controller->boot($this);
            $controller->run($input);
            $controller->teardown();
            exit;
        }

        $this->runSingle($input);
    }

    /**
     * @param CommandCall $input
     */
    protected function runSingle(CommandCall $input)
    {
        try {
            $callable = $this->command_registry->getCallable($input->command);
            call_user_func($callable, $input);
        } catch (\Exception $e) {
            $this->getPrinter()->display("ERROR: " . $e->getMessage());
            $this->printSignature();
            exit;
        }
    }

}