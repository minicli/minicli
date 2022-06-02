<?php

declare(strict_types=1);

namespace Minicli;

use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;
use Minicli\Exception\CommandNotFoundException;
use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\OutputHandler;

class App
{
    /**
     * app signature
     *
     * @var string
     */
    protected string $appSignature;

    /**
     * app services
     *
     * @var array
     */
    protected array $services = [];

    /**
     * app loaded services
     *
     * @var array
     */
    protected array $loadedServices = [];

    /**
     * App constructor
     *
     * @param array $config
     * @param string $signature
     */
    public function __construct(array $config = [], string $signature = './minicli help')
    {
        $config = array_merge([
            'app_path' => __DIR__ . '/../app/Command',
            'theme' => '',
            'debug' => true,
        ], $config);

        $this->addService('config', new Config($config));
        $commandsPath = $this->config->app_path;
        if (!is_array($commandsPath)) {
            $commandsPath = [ $commandsPath ];
        }

        $commandSources = [];
        foreach ($commandsPath as $path) {
            if (str_starts_with($path, '@')) {
                $path = str_replace('@', $this->getAppRoot() . '/vendor/', $path) . '/Command';
            }
            $commandSources[] = $path;
        }
        $this->addService('commandRegistry', new CommandRegistry($commandSources));

        $this->setSignature($signature);
        $this->setTheme($this->config->theme);
    }

    /**
     * @return string
     */
    public function getAppRoot(): string
    {
        $root_app = dirname(__DIR__);

        if (!is_file($root_app . '/vendor/autoload.php')) {
            $root_app = dirname(__DIR__, 4);
        }

        return $root_app;
    }

    /**
     * Magic method implements lazy loading for services
     *
     * @param string $name
     * @return ServiceInterface|null
     */
    public function __get(string $name): ?ServiceInterface
    {
        if (!array_key_exists($name, $this->services)) {
            return null;
        }

        if (!array_key_exists($name, $this->loadedServices)) {
            $this->loadService($name);
        }

        return $this->services[$name];
    }

    /**
     * add app service
     *
     * @param string $name
     * @param ServiceInterface $service
     * @return void
     */
    public function addService(string $name, ServiceInterface $service): void
    {
        $this->services[$name] = $service;
    }

    /**
     * load app service
     *
     * @param string $name
     * @return void
     */
    public function loadService(string $name): void
    {
        $this->loadedServices[$name] = $this->services[$name]->load($this);
    }

    /**
     * Shortcut for accessing the Output Handler
     *
     * @return OutputHandler
     */
    public function getPrinter(): OutputHandler
    {
        return $this->printer;
    }

    /**
     * Shortcut for setting the Output Handler
     *
     * @param OutputHandler $outputPrinter
     * @return void
     */
    public function setOutputHandler(OutputHandler $outputPrinter): void
    {
        $this->services['printer'] = $outputPrinter;
    }

    /**
     * get app signature
     *
     * @return string
     */
    public function getSignature(): string
    {
        return $this->appSignature;
    }

    /**
     * print signature
     *
     * @return void
     */
    public function printSignature(): void
    {
        $this->getPrinter()->display($this->getSignature());
    }

    /**
     * set signature
     *
     * @param string $appSignature
     * @return void
     */
    public function setSignature(string $appSignature): void
    {
        $this->appSignature = $appSignature;
    }

    /**
     * Set the Output Handler based on the App's theme config setting
     *
     * @param string $loadedServices
     * @return void
     */
    public function setTheme(string $loadedServices): void
    {
        $output = new OutputHandler();

        $output->registerFilter(
            (new ThemeHelper($loadedServices))
            ->getOutputFilter()
        );

        $this->addService('printer', $output);
    }

    /**
     * register app command
     *
     * @param string $name
     * @param callable $callable
     * @return void
     */
    public function registerCommand(string $name, callable $callable): void
    {
        $this->commandRegistry->registerCommand($name, $callable);
    }

    /**
     * run command
     *
     * @param array $argv
     * @return void
     *
     * @throws CommandNotFoundException
     */
    public function runCommand(array $argv = []): void
    {
        $input = new CommandCall($argv);

        if (count($input->args) < 2) {
            $this->printSignature();
            return;
        }

        $controller = $this->commandRegistry->getCallableController($input->command, $input->subcommand);

        if ($controller instanceof ControllerInterface) {
            $controller->boot($this);
            $controller->run($input);
            $controller->teardown();
            return;
        }

        $this->runSingle($input);
    }

    /**
     * run single
     *
     * @param CommandCall $input
     * @throws CommandNotFoundException
     *
     * @return bool
     */
    protected function runSingle(CommandCall $input): bool
    {
        try {
            $callable = $this->commandRegistry->getCallable($input->command);
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
