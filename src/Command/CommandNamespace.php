<?php

declare(strict_types=1);

namespace Minicli\Command;

use Minicli\ControllerInterface;

class CommandNamespace
{
    /**
     * @param string $name
     * @param array<string, ControllerInterface> $controllers
     */
    public function __construct(
        protected string $name,
        protected array $controllers = []
    ) {
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
     * @return array<string, ControllerInterface>
     */
    public function loadControllers(string $commandsPath): array
    {
        $controllers = glob($commandsPath . '/' . $this->getName() . '/*Controller.php');

        if ($controllers === false) {
            return $this->getControllers();
        }

        foreach ($controllers as $controllerFile) {
            $this->loadCommandMap($controllerFile);
        }

        return $this->getControllers();
    }

    /**
     * get controllers
     *
     * @return array<string, ControllerInterface>
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

        /** @var ControllerInterface $controller */
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
        $file = file($filename);
        if ($file === false) {
            return '';
        }

        $lines = preg_grep('/^namespace /', $file);
        if ($lines === false) {
            return '';
        }

        $namespaceLine = trim(array_shift($lines));
        $match = [];
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);

        return array_pop($match);
    }
}
