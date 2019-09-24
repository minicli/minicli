<?php

namespace Minicli;

class App
{
    protected $printer;

    protected $command_registry;

    public function __construct()
    {
        $this->printer = new CliPrinter();
        $this->command_registry = new CommandRegistry();
    }

    public function getPrinter()
    {
        return $this->printer;
    }

    public function registerController($name, CommandController $controller)
    {
        $this->command_registry->registerController($name, $controller);
    }

    public function registerCommand($name, $callable)
    {
        $this->command_registry->registerCommand($name, $callable);
    }

    public function runCommand(array $argv = [], $default_command = 'help')
    {
        $command_name = $default_command;

        if (isset($argv[1])) {
            $command_name = $argv[1];
        }

        call_user_func($this->getCallable($command_name), $argv);
    }

    protected function getCallable($command_name)
    {
        $controller = $this->command_registry->getController($command_name);

        if ($controller instanceof CommandController) {
            return [ $controller, 'run' ];
        }

        $command = $this->command_registry->getCommand($command_name);
        if ($command === null) {
            $this->getPrinter()->display("ERROR: Command \"$command_name\" not found.");
            exit;
        }

        return $command;
    }
}