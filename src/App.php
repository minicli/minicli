<?php

namespace Minicli;

use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;
use Minicli\Exception\CommandNotFoundException;
use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\OutputHandler;

class App
{
    /** @var  string  */
    protected $app_signature;

    /** @var  array */
    protected $services = [];

    /** @var array  */
    protected $loaded_services = [];

    /**
     * App constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'app_path' => __DIR__ . '/../app/Command',
            'theme' => '',
            'debug' => true,
        ], $config);

        $this->addService('config', new Config($config));
        $this->addService('command_registry', new CommandRegistry($this->config->app_path));

        $this->setSignature('./minicli help');
        $this->setTheme($this->config->theme);
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
     * Shortcut for accessing the Output Handler
     * @return OutputHandler
     */
    public function getPrinter(): OutputHandler
    {
        return $this->printer;
    }

    /**
     * Shortcut for setting the Output Handler
     * @param OutputHandler $output_printer
     */
    public function setOutputHandler(OutputHandler $output_printer)
    {
        $this->services['printer'] = $output_printer;
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
        $this->getPrinter()->display($this->getSignature());
    }
    /**
     * @param string $app_signature
     */
    public function setSignature($app_signature)
    {
        $this->app_signature = $app_signature;
    }

    /**
     * Set the Output Handler based on the App's theme config setting.
     * @param string $theme_config
     */
    public function setTheme(string $theme_config)
    {
        $output = new OutputHandler();

        $output->registerFilter(
            (new ThemeHelper($theme_config))
            ->getOutputFilter()
        );

        $this->addService('printer', $output);
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
     * @throws CommandNotFoundException
     */
    public function runCommand(array $argv = [])
    {
        $input = new CommandCall($argv);

        if (count($input->args) < 2) {
            $this->printSignature();
            return;
        }

        $controller = $this->command_registry->getCallableController($input->command, $input->subcommand);

        if ($controller instanceof ControllerInterface) {
            $controller->boot($this);
            $controller->run($input);
            $controller->teardown();
            return;
        }

        $this->runSingle($input);
    }

    /**
     * @param CommandCall $input
     * @throws CommandNotFoundException
     * @return bool
     */
    protected function runSingle(CommandCall $input)
    {
        try {
            $callable = $this->command_registry->getCallable($input->command);
        } catch (\Exception $e) {
            if (!$this->config->debug) {
                $this->getPrinter()->error($e->getMessage());
                return false;
            }
            throw $e;
        }

        if (is_callable($callable)) {
            call_user_func($callable, $input);
            return true;
        }

        if (!$this->config->debug) {
            $this->getPrinter()->error("The registered command is not a callable function.");
            return false;
        }

        throw new CommandNotFoundException("The registered command is not a callable function.");
    }
}
