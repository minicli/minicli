<?php

namespace Minicli\Command;

use Minicli\App;
use Minicli\Exception\CommandNotFoundException;
use Minicli\ServiceInterface;

class CommandRegistry implements ServiceInterface
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
    }

    public function load(App $app)
    {
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
    public function getCallableController($command, $subcommand = "default")
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
            throw new CommandNotFoundException(sprintf("Command \"%s\" not found.", $command));
        }

        return $single_command;
    }

    /**
     * @return array
     */
    public function getCommandMap()
    {
        $map = [];

        foreach ($this->default_registry as $command => $callback) {
            $map[$command] = $callback;
        }

        /**
         * @var  string $command
         * @var  CommandNamespace $namespace
         */
        foreach ($this->namespaces as $command => $namespace) {
            $controllers = $namespace->getControllers();
            $subs = [];
            foreach ($controllers as $subcommand => $controller) {
                $subs[] = $subcommand;
            }

            $map[$command] = $subs;
        }

        return $map;
    }
}
