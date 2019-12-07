<?php

namespace Minicli;

class CommandRegistry
{
    /** @var string */
    protected $commands_path;

    /** @var array */
    protected $namespaces = [];

    /** @var array */
    protected $default_registry = [];

    /**
     * CommandRegistry constructor.
     * @param string $commands_path
     */
    public function __construct($commands_path)
    {
        $this->commands_path = $commands_path;
        $this->autoloadNamespaces();
    }

    /**
     * @return void
     */
    public function autoloadNamespaces()
    {
        foreach (glob($this->getCommandsPath() . '/*', GLOB_ONLYDIR) as $namespace_path) {
            $this->registerNamespace(basename($namespace_path));
        }
    }

    /**
     * @param string $command_namespace
     * @return void
     */
    public function registerNamespace($command_namespace)
    {
        $namespace = new CommandNamespace($command_namespace);
        $namespace->loadControllers($this->getCommandsPath());
        $this->namespaces[strtolower($command_namespace)] = $namespace;
    }

    /**
     * @param string $command
     * @return CommandNamespace
     */
    public function getNamespace($command)
    {
        return isset($this->namespaces[$command]) ? $this->namespaces[$command] : null;
    }

    /**
     * @return string
     */
    public function getCommandsPath()
    {
        return $this->commands_path;
    }

    /**
     * Registers an anonymous function as single command.
     * @param string $name
     * @param callable $callable
     */
    public function registerCommand($name, $callable)
    {
        $this->default_registry[$name] = $callable;
    }

    /**
     * @param string $command
     * @return callable|null
     */
    public function getCommand($command)
    {
        return isset($this->default_registry[$command]) ? $this->default_registry[$command] : null;
    }

    /**
     * @param string $command
     * @param string $subcommand
     * @return CommandController | null
     */
    public function getCallableController($command, $subcommand = null)
    {
        $namespace = $this->getNamespace($command);

        if ($namespace !== null) {
            return $namespace->getController($subcommand);
        }

        return null;
    }

    /**
     * @param string $command
     * @return callable|null
     * @throws \Exception
     */
    public function getCallable($command)
    {
        $single_command = $this->getCommand($command);
        if ($single_command === null) {
            throw new \Exception(sprintf("Command \"%s\" not found.", $command));
        }

        return $single_command;
    }
}