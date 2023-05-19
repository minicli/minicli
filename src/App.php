<?php

declare(strict_types=1);

namespace Minicli;

use BadMethodCallException;
use Closure;
use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;
use Minicli\DI\Container;
use Minicli\Exception\CommandNotFoundException;
use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\OutputHandler;
use Throwable;

/**
 * @property Config $config
 * @property OutputHandler $printer
 * @property CommandRegistry $commandRegistry
 * @mixin OutputHandler
 */
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
     * @var array<string, ServiceInterface|Closure>
     */
    protected array $services = [];

    /**
     * app loaded services
     *
     * @var array<string, ServiceInterface|Closure|bool>
     */
    protected array $loadedServices = [];

    /**
     * The DI Container.
     *
     * @var Container
     */
    protected Container $container;

    /**
     * App constructor
     *
     * @param array<string, mixed> $config
     * @param string $signature
     */
    public function __construct(
        array $config = [],
        string $signature = './minicli help',
    ) {
        $this->container = Container::getInstance();

        $this->boot($config, $signature);
    }

    /**
     * @param array<string, mixed> $config
     * @param string $signature
     */
    public function boot(array $config, string $signature): void
    {
        $config = array_merge([
            'app_path' => __DIR__.'/../app/Command',
            'theme' => '',
            'debug' => true,
        ], $config);

        $this->setSignature($signature);

        $this->addService('config', new Config($config));
        $commandsPath = $this->config->app_path;
        if ( ! is_array($commandsPath)) {
            $commandsPath = [$commandsPath];
        }

        $commandSources = [];
        foreach ($commandsPath as $path) {
            if (str_starts_with($path, '@')) {
                $path = str_replace('@', $this->getAppRoot().'/vendor/', $path).'/Command';
            }
            $commandSources[] = $path;
        }
        $this->addService('commandRegistry', new CommandRegistry($commandSources));
        $this->setTheme($this->config->theme);
    }

    /**
     * @return string
     */
    public function getAppRoot(): string
    {
        $root_app = dirname(__DIR__);

        if ( ! is_file($root_app.'/vendor/autoload.php')) {
            $root_app = dirname(__DIR__, 4);
        }

        return $root_app;
    }

    /**
     * Magic method implements lazy loading for services
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        if ( ! array_key_exists($name, $this->services)) {
            return null;
        }

        if ( ! array_key_exists($name, $this->loadedServices)) {
            $this->loadService($name);
        }

        return $this->services[$name];
    }

    /**
     * @param string $name
     * @param array<int,mixed> $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this->getPrinter(), $name)) {
            return $this->getPrinter()->$name(...$arguments);
        }

        throw new BadMethodCallException("Method {$name} does not exist.");
    }

    /**
     * add app service
     *
     * @param string $name
     * @param ServiceInterface|Closure $service
     * @return void
     */
    public function addService(string $name, ServiceInterface|Closure $service): void
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
        $service = $this->services[$name];

        if ($service instanceof Closure) {
            $this->services[$name] = $service($this);
            $this->loadedServices[$name] = true;
            return;
        }

        $service->load($this);
        $this->loadedServices[$name] = true;
    }

    /**
     * Shortcut for accessing the Output Handler
     *
     * @return OutputHandler
     * @deprecated
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
        $this->display($this->getSignature());
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
     * @param array<int,string> $argv
     * @return void
     *
     * @throws CommandNotFoundException|Throwable
     */
    public function runCommand(array $argv = []): void
    {
        $input = new CommandCall($argv);

        if (count($input->args) < 2) {
            $this->printSignature();
            return;
        }

        $controller = $this->commandRegistry->getCallableController((string) $input->command, $input->subcommand);

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
     * @return bool
     * @throws CommandNotFoundException|Throwable
     *
     */
    protected function runSingle(CommandCall $input): bool
    {
        try {
            $callable = $this->commandRegistry->getCallable((string) $input->command);
        } catch (Throwable $exception) {
            if ( ! $this->config->debug) {
                $this->error($exception->getMessage());
                return false;
            }
            throw $exception;
        }

        if (is_callable($callable)) {
            call_user_func($callable, $input);
            return true;
        }

        if ( ! $this->config->debug) {
            $this->error("The registered command is not a callable function.");
            return false;
        }

        throw new CommandNotFoundException("The registered command is not a callable function.");
    }
}
