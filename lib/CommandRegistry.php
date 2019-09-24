<?php

namespace Minicli;

class CommandRegistry
{
    protected $registry = [];

    protected $controllers = [];

    public function registerController($command_name, CommandController $controller)
    {
        $this->controllers = [ $command_name => $controller ];
    }

    public function registerCommand($name, $callable)
    {
        $this->registry[$name] = $callable;
    }

    public function getController($command)
    {
        return isset($this->controllers[$command]) ? $this->controllers[$command] : null;
    }

    public function getCommand($command)
    {
        return isset($this->registry[$command]) ? $this->registry[$command] : null;
    }
}