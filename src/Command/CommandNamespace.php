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
    public function loadControllers($commands_path)
    {
        foreach (glob($commands_path . '/' . $this->getName() . '/*Controller.php') as $controller_file) {
            $this->loadCommandMap($controller_file);
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
     * @param $command_name
     * @return CommandController
     */
    public function getController($command_name)
    {
        return isset($this->controllers[$command_name]) ? $this->controllers[$command_name] : null;
    }

    /**
     * @param string $controller_file
     */
    protected function loadCommandMap($controller_file)
    {
        $filename = basename($controller_file);

        $controller_class = str_replace('.php', '', $filename);
        $command_name = strtolower(str_replace('Controller', '', $controller_class));
        $full_class_name = sprintf("%s\\%s", $this->getNamespace($controller_file), $controller_class);

        /** @var CommandController $controller */
        $controller = new $full_class_name();
        $this->controllers[$command_name] = $controller;
    }

    protected function getNamespace($filename)
    {
        $lines = preg_grep('/^namespace /', file($filename));
        $namespace_line = array_shift($lines);
        $match = [];
        preg_match('/^namespace (.*);$/', $namespace_line, $match);

        return array_pop($match);
    }
}
