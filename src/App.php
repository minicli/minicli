<?php

declare(strict_types=1);

namespace Minicli;

use BadMethodCallException;
use Closure;
use Minicli\Attributes\Config;
use Minicli\Attributes\Service;
use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;
use Minicli\Config\AppConfig;
use Minicli\Container\Container;
use Minicli\Contracts\ControllerInterface;
use Minicli\Contracts\ServiceInterface;
use Minicli\Contracts\ThemeInterface;
use Minicli\Exceptions\BindingResolutionException;
use Minicli\Exceptions\CommandNotFoundException;
use Minicli\Exceptions\MissingParametersException;
use Minicli\Log\Logger;
use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\OutputHandler;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Throwable;

/**
 * @property Logger $logger
 * @property OutputHandler $printer
 * @property CommandRegistry $commandRegistry
 * @property string $appSignature
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
        $this->loadConfig();
        $this->loadServices();

        /** @var AppConfig $config */
        $config = $this->config('app');

        $commandsPath = $config->commandPaths;
        $commandSources = [];
        foreach ($commandsPath as $path) {
            if (str_starts_with((string) $path, '@')) {
                $path = str_replace('@', $this->basePath() . '/vendor/', $path) . '/Commands';
            }
            $commandSources[] = $path;
        }
        $this->addService('commandRegistry', new CommandRegistry($commandSources));
        $this->setTheme($config->theme);
    }

    public function getAppRoot(): string
    {
        $root_app = dirname(__DIR__);

        if (! is_file($root_app . '/vendor/autoload.php')) {
            return dirname(__DIR__, 4);
        }

        return $root_app;
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

    public function getSignature(): string
    {
        return $this->appSignature;
    }

    public function printSignature(): void
    {
        $this->display($this->appSignature);
    }

    public function setSignature(string $appSignature): void
    {
        $this->container->remove('appSignature');
        $this->container->bind('appSignature', fn (): string => $appSignature);
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

    public function registerCommand(string $name, callable $callable): void
    {
        $this->commandRegistry->registerCommand($name, $callable);
    }

    /**
     * @param  array<string, callable>  $commands
     */
    public function registerCommands(array $commands): void
    {
        foreach ($commands as $name => $callable) {
            $this->registerCommand($name, $callable);
        }
    }

    /**
     * @param  array<int,string>  $argv
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
            try {
                $controller->boot($this, $input);
                $controller->run($input);
                $controller->teardown();

                return;
            } catch (MissingParametersException $exception) {
                $this->logger->error($exception->getMessage());
                $this->error($exception->getMessage());

                return;
            }
        }

        $this->runSingle($input);
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
     * @throws CommandNotFoundException|Throwable
     */
    private function runSingle(CommandCall $input): bool
    {
        /** @var AppConfig $config */
        $config = $this->config('app');

        try {
            $callable = $this->commandRegistry->getCallable((string) $input->command);
        } catch (Throwable $exception) {
            if (! $config->debug) {
                $this->logger->error($exception->getMessage());
                $this->error($exception->getMessage());

                return false;
            }
            throw $exception;
        }

        if (is_callable($callable)) {
            call_user_func($callable, $input);

            return true;
        }

        if (! $config->debug) {
            $this->error('The registered command is not a callable function.');

            return false;
        }

        throw new CommandNotFoundException('The registered command is not a callable function.');
    }

    private function bindPaths(?string $appRoot): void
    {
        $appRoot ??= $this->getAppRoot();

        $this->container->bind('base_path', fn (): string => $appRoot);
        $this->container->bind('config_path', fn (): string => "{$appRoot}/config");
        $this->container->bind('logs_path', fn (): string => "{$appRoot}/logs");
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    private function loadConfig(): void
    {
        $configPath = $this->configPath();

        if (! is_dir($configPath)) {
            return;
        }

        $configFiles = glob($configPath . '/*.php');

        if ($configFiles === false || $configFiles === []) {
            return;
        }

        foreach ($configFiles as $configFile) {
            require_once $configFile;

            $className = basename($configFile, '.php');

            if (! class_exists($className)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($className);
            $configAttributes = $reflectionClass->getAttributes(Config::class);

            if ($configAttributes === []) {
                throw new RuntimeException("Configuration class {$className} must have a Config attribute.");
            }

            $configAttribute = $configAttributes[0]->newInstance();
            $configName = $configAttribute->name;

            $configInstance = new $className();
            $this->container->singleton($this->configKey($configName), fn (): object => $configInstance);
        }
    }

    private function loadServices(): void
    {
        $this->loadDefaultServices();

        $basePath = $this->basePath();

        if (! is_dir($basePath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $processedClasses = [];

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $filePath = $file->getRealPath();
            // Skip vendor and config directories
            if (str_contains((string) $filePath, '/vendor/')) {
                continue;
            }
            if (str_contains((string) $filePath, '/config/')) {
                continue;
            }

            // Track classes before requiring file
            $classesBefore = get_declared_classes();

            require_once $filePath;

            // Get newly declared classes from this file
            $classesAfter = get_declared_classes();
            $newClasses = array_diff($classesAfter, $classesBefore);

            foreach ($newClasses as $className) {
                // Skip if already processed
                if (isset($processedClasses[$className])) {
                    continue;
                }

                $processedClasses[$className] = true;

                if (! class_exists($className)) {
                    continue;
                }

                $reflectionClass = new ReflectionClass($className);

                // Check if class has Service attribute
                $serviceAttributes = $reflectionClass->getAttributes(Service::class);

                if ($serviceAttributes === []) {
                    continue;
                }

                // Check if class implements ServiceInterface
                if (! $reflectionClass->implementsInterface(ServiceInterface::class)) {
                    continue;
                }

                $serviceAttribute = $serviceAttributes[0]->newInstance();
                $serviceName = $serviceAttribute->name;

                /** @var ServiceInterface $serviceInstance */
                $serviceInstance = new $className();
                $this->addService($serviceName, $serviceInstance);
            }
        }
    }

    private function loadDefaultServices(): void
    {
        $this->addService('logger', new Logger());
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
