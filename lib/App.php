<?php

namespace Minicli;

use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;
use Minicli\Output\CliPrinter;

class App
{
    /** @var  string  */
    protected $app_signature;

    /** @var  array */
    protected $services = [];

    /** @var array  */
    protected $loaded_services = [];

    public function __construct(array $config = null)
    {
        $config = array_merge([
            'app_path' => __DIR__ . '/../app/Command',
            'theme'    => 'regular',
        ], $config);

        $this->setSignature('./minicli help');

        $this->addService('config', new Config($config));
        $this->addService('command_registry', new CommandRegistry($this->config->app_path));
        $this->addService('printer', new CliPrinter());
    }

    /**
     * Magic method implements lazy loading for services.
     * @param string $name
     * @return ServiceInterface|null
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->services)) {
            return null;
        }

        if (!array_key_exists($name, $this->loaded_services)) {
            $this->loadService($name);
        }

        return $this->services[$name];
    }

    /**
     * @param string $name
     * @param ServiceInterface $service
     */
    public function addService($name, ServiceInterface $service)
    {
        $this->services[$name] = $service;
    }

    /**
     * @param string $name
     */
    public function loadService($name)
    {
        $this->loaded_services[$name] = $this->services[$name]->load($this);
    }

    /**
     * @return OutputInterface
     */
    public function getPrinter()
    {
        return $this->printer;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->app_signature;
    }

    /**
     * @return void
     */
    public function printSignature()
    {
        $this->getPrinter()->display(sprintf("usage: %s", $this->getSignature()));
    }
    /**
     * @param string $app_signature
     */
    public function setSignature($app_signature)
    {
        $this->app_signature = $app_signature;
    }

    /**
     * @param string $name
     * @param callable $callable
     */
    public function registerCommand($name, $callable)
    {
        $this->command_registry->registerCommand($name, $callable);
    }

    /**
     * @param array $argv
     */
    public function runCommand(array $argv = [])
    {
        $input = new CommandCall($argv);

        if (count($input->args) < 2) {
            $this->printSignature();
            exit;
        }

        $controller = $this->command_registry->getCallableController($input->command, $input->subcommand);

        if ($controller instanceof ControllerInterface) {
            $controller->boot($this);
            $controller->run($input);
            $controller->teardown();
            exit;
        }

        $this->runSingle($input);
    }

    /**
     * @param CommandCall $input
     */
    protected function runSingle(CommandCall $input)
    {
        try {
            $callable = $this->command_registry->getCallable($input->command);
            call_user_func($callable, $input);
        } catch (\Exception $e) {
            $this->getPrinter()->display("ERROR: " . $e->getMessage());
            $this->printSignature();
            exit;
        }
    }

}