<?php

namespace Minicli\Command;

use Minicli\App;
use Minicli\Exception\CommandNotFoundException;
use Minicli\ServiceInterface;

class CommandRegistry implements ServiceInterface
{
    /** @var string */
    protected $commandsPath;

    /** @var array */
    protected $namespaces = [];

    /** @var array */
    protected $defaultRegistry = [];

    /**
     * CommandRegistry constructor.
     * @param string $commandsPath
     */
    public function __construct($commandsPath)
    {
        $this->commandsPath = $commandsPath;
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
        foreach (glob($this->getCommandsPath() . '/*', GLOB_ONLYDIR) as $namespacePath) {
            $this->registerNamespace(basename($namespacePath));
        }
    }

    /**
     * @param string $commandNamespace
     * @return void
     */
    public function registerNamespace($commandNamespace)
    {
        $namespace = new CommandNamespace($commandNamespace);
        $namespace->loadControllers($this->getCommandsPath());
        $this->namespaces[strtolower($commandNamespace)] = $namespace;
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
        return $this->commandsPath;
    }

    /**
     * Registers an anonymous function as single command.
     * @param string $name
     * @param callable $callable
     */
    public function registerCommand($name, $callable)
    {
        $this->defaultRegistry[$name] = $callable;
    }

    /**
     * @param string $command
     * @return callable|null
     */
    public function getCommand($command)
    {
        return isset($this->defaultRegistry[$command]) ? $this->defaultRegistry[$command] : null;
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

        foreach ($this->defaultRegistry as $command => $callback) {
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
