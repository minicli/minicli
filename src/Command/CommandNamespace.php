<?php
namespace Minicli\Command;

class CommandNamespace
{
    /** @var  string */
    protected $name;

    /** @var array  */
    protected $controllers = [];

    /**
     * CommandNamespace constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Load namespace controllers
     * @return array
     */
    public function loadControllers($commandsPath)
    {
        foreach (glob($commandsPath . '/' . $this->getName() . '/*Controller.php') as $controllerFile) {
            $this->loadCommandMap($controllerFile);
        }

        return $this->getControllers();
    }

    /**
     * @return array
     */
    public function getControllers()
    {
        return $this->controllers;
    }

    /**
     * @param $commandName
     * @return CommandController
     */
    public function getController($commandName)
    {
        return isset($this->controllers[$commandName]) ? $this->controllers[$commandName] : null;
    }

    /**
     * @param string $controllerFile
     */
    protected function loadCommandMap($controllerFile)
    {
        $filename = basename($controllerFile);

        $controllerClass = str_replace('.php', '', $filename);
        $commandName = strtolower(str_replace('Controller', '', $controllerClass));
        $full_class_name = sprintf("%s\\%s", $this->getNamespace($controllerFile), $controllerClass);

        /** @var CommandController $controller */
        $controller = new $full_class_name();
        $this->controllers[$commandName] = $controller;
    }

    protected function getNamespace($filename)
    {
        $lines = preg_grep('/^namespace /', file($filename));
        $namespaceLine = trim(array_shift($lines));
        $match = [];
        preg_match('/^namespace (.*);$/', $namespaceLine, $match);

        return array_pop($match);
    }
}
