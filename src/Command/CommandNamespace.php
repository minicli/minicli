<?php

declare(strict_types=1);

namespace Minicli\Command;

use Minicli\ControllerInterface;

class CommandNamespace
{
    /**
     * name
     *
     * @param string $name
     */
    protected string $name;

    /**
     * controllers
     *
     * @param array $controllers
     */
    protected array $controllers = [];

    /**
     * CommandNamespace constructor
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Load namespace controllers
     *
     * @param string $commandsPath
     * @return array
     */
    public function loadControllers(string $commandsPath): array
    {
        foreach (glob($commandsPath . '/' . $this->getName() . '/*Controller.php') as $controllerFile) {
            $this->loadCommandMap($controllerFile);
        }

        return $this->getControllers();
    }

    /**
     * get controllers
     *
     * @return array
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    /**
     * @param string $commandName
     *
     * @return ControllerInterface|null
     */
    public function getController(string $commandName): ?ControllerInterface
    {
        return $this->controllers[$commandName] ?? null;
    }

    /**
     * load command map
     *
     * @param string $controllerFile
     * @return void
     */
    protected function loadCommandMap(string $controllerFile): void
    {
        $filename = basename($controllerFile);

        $controllerClass = str_replace('.php', '', $filename);
        $commandName = strtolower(str_replace('Controller', '', $controllerClass));
        $fullClassName = sprintf("%s\\%s", $this->getNamespace($controllerFile), $controllerClass);

        $controller = new $fullClassName();
        $this->controllers[$commandName] = $controller;
    }

    /**
     * get namespace
     *
     * @param string $filename
     * @return string
     */
    protected function getNamespace(string $filename): string
    {
        $lines = preg_grep('/^namespace /', file($filename));
        $namespaceLine = trim(array_shift($lines));
        $match = [];
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);

        return array_pop($match);
    }
}
