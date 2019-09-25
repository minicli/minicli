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

        try {
            call_user_func($this->command_registry->getCallable($command_name), $argv);
        } catch (\Exception $e) {
            $this->getPrinter()->display("ERROR: " . $e->getMessage());
            exit;
        }
    }
}