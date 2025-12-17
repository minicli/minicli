<?php

declare(strict_types=1);

namespace Minicli;

use BadMethodCallException;
use Closure;
use Minicli\Config\AppConfig;
use Minicli\Console\CommandCall;
use Minicli\Console\CommandInfo;
use Minicli\Console\CommandRegistry;
use Minicli\Console\ExitCode;
use Minicli\Container\Container;
use Minicli\Contracts\ServiceInterface;
use Minicli\Contracts\ThemeInterface;
use Minicli\Exceptions\BindingResolutionException;
use Minicli\Exceptions\CommandNotFoundException;
use Minicli\Log\Logger;
use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\OutputHandler;
use Minicli\Support\ConfigLoader;
use Minicli\Support\ServiceLoader;
use ReflectionException;
use Throwable;

/**
 * @property Logger $logger
 * @property OutputHandler $printer
 * @property CommandRegistry $commandRegistry
 *
 * @mixin OutputHandler
 */
final readonly class App
{
    public string $me;

    private Container $container;

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function __construct(?string $appRoot = null)
    {
        $this->container = Container::getInstance();

        $this->bindPaths($appRoot);
        $this->boot();

        $this->me = $this->findBinFileName();
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function __get(string $name): mixed
    {
        return $this->container->has($name)
            ? $this->container->get($name)
            : null;
    }

    /**
     * @param  array<int,mixed>  $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (method_exists($this->printer, $name)) {
            return $this->printer->{$name}(...$arguments);
        }

        throw new BadMethodCallException("Method {$name} does not exist.");
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function boot(): void
    {
        new ConfigLoader()->load($this);
        new ServiceLoader()->load($this);

        /** @var AppConfig $config */
        $config = $this->config('app');
        $this->addService('commandRegistry', new CommandRegistry());
        $this->setTheme($config->theme);
    }

    public function appRoot(): string
    {
        $root = dirname(__DIR__);
        if (! is_file("{$root}/vendor/autoload.php")) {
            return dirname(__DIR__, 4);
        }

        return $root;
    }

    public function addService(string $name, ServiceInterface|Closure $service): void
    {
        if ($service instanceof Closure) {
            $this->container->bind($name, fn () => $service($this));

            return;
        }

        $service->load($this);
        $this->container->bind($name, fn (): ServiceInterface => $service);
    }

    public function addConfig(string $name, object $configInstance): void
    {
        $this->container->singleton($this->configKey($name), fn (): object => $configInstance);
    }

    public function setOutputHandler(OutputHandler $outputPrinter): void
    {
        $this->container->remove('printer');
        $this->addService('printer', $outputPrinter);
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function config(string $name): ?object
    {
        $configKey = $this->configKey($name);

        return $this->container->has($configKey)
            ? $this->container->get($configKey)
            : null;
    }

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function basePath(): string
    {
        return $this->container->get('base_path');
    }

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function configPath(): string
    {
        return $this->container->get('config_path');
    }

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function logsPath(): string
    {
        return $this->container->get('logs_path');
    }

    /**
     * @param  class-string<ThemeInterface>  $theme
     */
    public function setTheme(?string $theme): void
    {
        $output = new OutputHandler();
        $output->registerFilter(new ThemeHelper($theme)->getOutputFilter());

        $this->addService('printer', $output);
    }

    public function registerCommand(string $name, CommandInfo $commandInfo): void
    {
        $this->commandRegistry->registerCommand($name, $commandInfo);
    }

    /**
     * @param  array<string, CommandInfo>  $commands
     */
    public function registerCommands(array $commands): void
    {
        foreach ($commands as $name => $commandInfo) {
            $this->registerCommand($name, $commandInfo);
        }
    }

    /**
     * @param  array<int,string>  $argv
     *
     * @throws CommandNotFoundException|Throwable
     */
    public function runCommand(array $argv = []): int
    {
        $input = new CommandCall($argv);

        if (count($input->args) < 2) {
            // Run help command by default
            $helpCommand = $this->commandRegistry->getCommand('help');
            if ($helpCommand !== null) {
                /** @var ExitCode $result */
                $result = ($helpCommand->callable)($input, $this);
            }

            return $result->value ?? ExitCode::Failure->value;
        }

        $commandName = $input->command;
        if ($input->subcommand !== null) {
            $commandName .= " {$input->subcommand}";
        }

        $command = $this->commandRegistry->getCommand($commandName);
        if ($command === null) {
            throw new CommandNotFoundException("Command '{$commandName}' not found.");
        }

        /** @var ExitCode $result */
        $result = ($command->callable)($input, $this);

        return $result->value;
    }

    /**
     * List all registered services.
     *
     * @return array<string, mixed>
     */
    public function listServices(): array
    {
        return $this->container->getBindings();
    }

    /**
     * Check if a service is registered.
     */
    public function hasService(string $serviceName): bool
    {
        return $this->container->has($serviceName);
    }

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function make(string $abstract): mixed
    {
        return $this->container->make($abstract);
    }

    private function bindPaths(?string $appRoot): void
    {
        $appRoot ??= $this->appRoot();

        $this->container->bind('base_path', fn (): string => $appRoot);
        $this->container->bind('config_path', fn (): string => "{$appRoot}/config");
        $this->container->bind('logs_path', fn (): string => "{$appRoot}/logs");
    }

    private function findBinFileName(): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // Index 0 is current method, index 1 is the instantiation
        return basename($backtrace[1]['file'] ?? 'minicli');
    }

    private function configKey(string $name): string
    {
        return "config_{$name}";
    }
}
