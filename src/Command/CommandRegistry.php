<?php

declare(strict_types=1);

namespace Minicli\Command;

use Minicli\App;
use Minicli\ControllerInterface;
use Minicli\ServiceInterface;
use Minicli\Exception\CommandNotFoundException;

class CommandRegistry implements ServiceInterface
{
    /**
     * commands path
     *
     * @param string $commandsPath
     */
    protected string $commandsPath;

    /**
     * namespaces
     *
     * @param array $namespaces
     */
    protected array $namespaces = [];

    /**
     * default registry
     *
     * @param array $defaultRegistry
     */
    protected array $defaultRegistry = [];

    /**
     * CommandRegistry constructor
     *
     * @param string $commandsPath
     */
    public function __construct(string $commandsPath)
    {
        $this->commandsPath = $commandsPath;
    }

    /**
     * load app
     *
     * @param App $app
     * @return void
     */
    public function load(App $app): void
    {
        $this->autoloadNamespaces();
    }

    /**
     * autoload namespaces
     *
     * @return void
     */
    public function autoloadNamespaces(): void
    {
        foreach (glob($this->getCommandsPath() . '/*', GLOB_ONLYDIR) as $namespacePath) {
            $this->registerNamespace(basename($namespacePath));
        }
    }

    /**
     * register namespace
     *
     * @param string $commandNamespace
     * @return void
     */
    public function registerNamespace(string $commandNamespace): void
    {
        $namespace = new CommandNamespace($commandNamespace);
        $namespace->loadControllers($this->getCommandsPath());
        $this->namespaces[strtolower($commandNamespace)] = $namespace;
    }

    /**
     * get namespace
     *
     * @param string $command
     * @return ?CommandNamespace
     */
    public function getNamespace(string $command): ?CommandNamespace
    {
        return $this->namespaces[$command] ?? null;
    }

    /**
     * get commands path
     *
     * @return string
     */
    public function getCommandsPath(): string
    {
        return $this->commandsPath;
    }

    /**
     * Registers an anonymous function as single command
     *
     * @param string $name
     * @param callable $callable
     * @return void
     */
    public function registerCommand(string $name, callable $callable): void
    {
        $this->defaultRegistry[$name] = $callable;
    }

    /**
     * get command
     *
     * @param string $command
     * @return callable|null
     */
    public function getCommand(string $command): ?callable
    {
        return $this->defaultRegistry[$command] ?? null;
    }

    /**
     * get callable controller
     *
     * @param string $command
     * @param string $subcommand
     * @return ControllerInterface|null
     */
    public function getCallableController(string $command, string $subcommand = "default"): ?ControllerInterface
    {
        $namespace = $this->getNamespace($command);

        if ($namespace !== null) {
            return $namespace->getController($subcommand);
        }

        return null;
    }

    /**
     * get callable
     *
     * @param string $command
     * @return callable|null
     *
     * @throws CommandNotFoundException
     */
    public function getCallable(string $command): ?callable
    {
        $singleCommand = $this->getCommand($command);
        if ($singleCommand === null) {
            throw new CommandNotFoundException(sprintf("Command \"%s\" not found.", $command));
        }

        return $singleCommand;
    }

    /**
     * get command map
     *
     * @return array
     */
    public function getCommandMap(): array
    {
        $map = [];

        foreach ($this->defaultRegistry as $command => $callback) {
            $map[$command] = $callback;
        }

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
