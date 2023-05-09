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
     * @param array<int, string> $commandsPath
     * @param array<string, CommandNamespace> $namespaces
     * @param array<string, callable> $defaultRegistry
     */
    public function __construct(
        protected array $commandsPath,
        protected array $namespaces = [],
        protected array $defaultRegistry = [],
    ) {
    }

    /**
     * load app
     *
     * @param App $app
     * @return void
     */
    public function load(App $app): void
    {
        foreach ($this->getCommandsPath() as $commandSource) {
            $this->autoloadNamespaces($commandSource);
        }
    }

    /**
     * autoload namespaces
     *
     * @param string $commandSource
     * @return void
     */
    public function autoloadNamespaces(string $commandSource): void
    {
        $paths = (array) glob($commandSource . '/*', GLOB_ONLYDIR);

        /**
         * @var string $namespacePath
         */
        foreach ($paths as $namespacePath) {
            if (file_exists($namespacePath . '/composer.json')) {
                //this looks like a 3rd party package, so lets run a sec check
            }
            $this->registerNamespace(basename($namespacePath), $commandSource);
        }
    }

    /**
     * register namespace
     *
     * @param string $commandNamespace
     * @param string $commandSource
     * @return void
     */
    public function registerNamespace(string $commandNamespace, string $commandSource): void
    {
        $namespace = new CommandNamespace($commandNamespace);
        $namespace->loadControllers($commandSource);
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
     * @return array<int, string>
     */
    public function getCommandsPath(): array
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

        return $namespace?->getController($subcommand);
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
     * @return array<string, callable|array<string>>
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
