<?php

declare(strict_types=1);

namespace Minicli;

use BadMethodCallException;
use Closure;
use Minicli\Command\CommandCall;
use Minicli\Command\CommandRegistry;
use Minicli\DI\Container;
use Minicli\Exception\CommandNotFoundException;
use Minicli\Exception\MissingParametersException;
use Minicli\Output\Helper\ThemeHelper;
use Minicli\Output\OutputHandler;
use ReflectionException;
use Throwable;

/**
 * @property Config $config
 * @property OutputHandler $printer
 * @property CommandRegistry $commandRegistry
 * @property string $appSignature
 * @property string $base_path
 * @property string $config_path
 * @mixin OutputHandler
 */
class App
{
    private const DEFAULT_SIGNATURE = './minicli help';

    protected Container $container;

    /**
     * @param array<string, mixed> $config
     * @param string $signature
     * @param string|null $appRoot
     * @throws Exception\BindingResolutionException|ReflectionException
     */
    public function __construct(
        array $config = [],
        string $signature = self::DEFAULT_SIGNATURE,
        ?string $appRoot = null
    ) {
        $this->container = Container::getInstance();

        $this->bindPaths($appRoot);
        $this->boot($config, $signature);
    }

    /**
     * @param array<string, mixed> $config
     * @param string $signature
     * @throws Exception\BindingResolutionException|ReflectionException
     */
    public function boot(array $config, string $signature): void
    {
        $this->loadConfig($config, $signature);
        $this->loadServices();

        $commandsPath = $this->config->app_path;
        if ( ! is_array($commandsPath)) {
            $commandsPath = [$commandsPath];
        }

        $commandSources = [];
        foreach ($commandsPath as $path) {
            if (str_starts_with($path, '@')) {
                $path = str_replace('@', $this->base_path.'/vendor/', $path).'/Command';
            }
            $commandSources[] = $path;
        }
        $this->addService('commandRegistry', new CommandRegistry($commandSources));
        $this->setTheme($this->config->theme);
    }

    public function getAppRoot(): string
    {
        $root_app = dirname(__DIR__);

        if ( ! is_file($root_app.'/vendor/autoload.php')) {
            $root_app = dirname(__DIR__, 4);
        }

        return $root_app;
    }

    /**
     * @throws Exception\BindingResolutionException|ReflectionException
     */
    public function __get(string $name): mixed
    {
        return $this->container->has($name)
            ? $this->container->get($name)
            : null;
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
     * @param string $name
     * @param ServiceInterface|Closure $service
     * @return void
     */
    public function addService(string $name, ServiceInterface|Closure $service): void
    {
        if ($service instanceof Closure) {
            $this->container->bind($name, fn () => $service($this));
            return;
        }

        $service->load($this);
        $this->container->bind($name, fn () => $service);
    }

    /**
     * @deprecated
     */
    public function getPrinter(): OutputHandler
    {
        return $this->printer;
    }

    public function setOutputHandler(OutputHandler $outputPrinter): void
    {
        $this->container->remove('printer');
        $this->addService('printer', $outputPrinter);
    }

    public function getSignature(): string
    {
        return $this->appSignature;
    }

    public function printSignature(): void
    {
        $this->display($this->getSignature());
    }

    public function setSignature(string $appSignature): void
    {
        $this->container->remove('appSignature');
        $this->container->bind('appSignature', fn () => $appSignature);
    }

    public function setTheme(string $theme): void
    {
        $output = new OutputHandler();

        $output->registerFilter(
            (new ThemeHelper($theme))
                ->getOutputFilter()
        );

        $this->addService('printer', $output);
    }

    public function registerCommand(string $name, callable $callable): void
    {
        $this->commandRegistry->registerCommand($name, $callable);
    }
    /**
     * @param array<string, callable> $commands
     * @return void
     */
    public function registerCommands(array $commands): void
    {
        foreach ($commands as $name => $callable) {
            $this->registerCommand($name, $callable);
        }
    }
    /**
     * @param array<int,string> $argv
     * @return void
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
                $this->error($exception->getMessage());

                return;
            }
        }

        $this->runSingle($input);
    }

    /**
     * @throws CommandNotFoundException|Throwable
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

    protected function bindPaths(?string $appRoot): void
    {
        $appRoot ??= $this->getAppRoot();

        $this->container->bind('base_path', fn () => $appRoot);
        $this->container->bind('config_path', fn () => "{$appRoot}/config");
    }

    /**
     * @param array<string,mixed> $config
     * @param string $signature
     * @return void
     * @throws Exception\BindingResolutionException|ReflectionException
     */
    protected function loadConfig(array $config, string $signature): void
    {
        $config = array_merge([
            'app_path' => $this->base_path.'/Command',
            'theme' => '',
            'debug' => true,
        ], $config);

        $this->addService('config', new Config(load_config($config, $this->config_path)));

        $appSignature = self::DEFAULT_SIGNATURE === $signature && $this->config->app_name
            ? $this->config->app_name
            : $signature;

        $this->setSignature($appSignature);
    }

    protected function loadServices(): void
    {
        $services = $this->config->services ?? [];
        if ([] === $services) {
            return;
        }

        foreach ($services as $name => $service) {
            $this->addService($name, new $service());
        }
    }
}
