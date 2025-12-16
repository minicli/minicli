<?php

declare(strict_types=1);

namespace Minicli\Console;

use Minicli\Contracts\ControllerInterface;

class CommandNamespace
{
    /**
     * @param  array<string, ControllerInterface>  $controllers
     */
    public function __construct(
        protected string $name,
        protected array $controllers = []
    ) {}

    /**
     * get name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Load namespace controllers
     *
     * @return array<string, ControllerInterface>
     */
    public function loadControllers(string $commandsPath): array
    {
        $controllers = (array) glob($commandsPath . '/' . $this->getName() . '/*Controller.php');

        /**
         * @var string $controllerFile
         */
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

    public function getController(string $commandName): ?ControllerInterface
    {
        return $this->controllers[$commandName] ?? null;
    }

    /**
     * load command map
     */
    protected function loadCommandMap(string $controllerFile): void
    {
        $filename = basename($controllerFile);

        $controllerClass = str_replace('.php', '', $filename);
        $commandName = mb_strtolower(str_replace('Controller', '', $controllerClass));
        $fullClassName = sprintf('%s\\%s', $this->getNamespace($controllerFile), $controllerClass);

        /** @var ControllerInterface $controller */
        $controller = new $fullClassName();
        $this->controllers[$commandName] = $controller;
    }

    /**
     * get namespace
     */
    protected function getNamespace(string $filename): string
    {
        $file = (array) file($filename);
        $lines = (array) preg_grep('/^namespace /', $file);
        $namespaceLine = trim((string) array_shift($lines));
        $match = [];
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);

        return (string) array_pop($match);
    }
}
