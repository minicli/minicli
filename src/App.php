<?php

declare(strict_types=1);

namespace Minicli;

use Closure;
use Minicli\Components\Component;
use Minicli\Components\Divider;
use Minicli\Components\Table\Row;
use Minicli\Components\Table\Table;
use Minicli\Components\Text;
use Minicli\Config\AppConfig;
use Minicli\Console\CommandCall;
use Minicli\Console\CommandInfo;
use Minicli\Console\CommandRegistry;
use Minicli\Console\ExitCode;
use Minicli\Console\GlobalFlag;
use Minicli\Container\Container;
use Minicli\Contracts\ServiceInterface;
use Minicli\Contracts\ThemeInterface;
use Minicli\Exceptions\BindingResolutionException;
use Minicli\Exceptions\CommandNotFoundException;
use Minicli\Log\Logger;
use Minicli\Output\Filter\ColorOutputFilter;
use Minicli\Support\ConfigLoader;
use Minicli\Support\ServiceLoader;
use ReflectionException;
use RuntimeException;
use Throwable;

/**
 * @property Logger $logger
 * @property CommandRegistry $commandRegistry
 * @property AppConfig $config
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
        $this->container = new Container();

        $this->bindPaths($appRoot);
        $this->boot();

        $this->me = $this->findBinFileName();
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function __get(string $name): mixed
    {
        if (! $this->container->has($name)) {
            return null;
        }

        return $this->container->get($name);
    }

    public function __isset(string $name): bool
    {
        return $this->container->has($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->container->bind($name, fn (): mixed => $value);
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function boot(): void
    {
        new ConfigLoader()->load($this);
        new ServiceLoader()->load($this);

        $this->addService('commandRegistry', new CommandRegistry());
        $this->setTheme();
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
        $this->container->singleton($service::class, fn (): ServiceInterface => $service);
    }

    public function addConfig(string $name, object $configInstance): void
    {
        $this->container->singleton($this->configKey($name), fn (): object => $configInstance);
    }

    /**
     * @throws BindingResolutionException|ReflectionException
     */
    public function config(string $name): ?object
    {
        $configKey = $this->configKey($name);

        if (! $this->container->has($configKey)) {
            return $name === 'app'
                ? new AppConfig(commandPaths: [$this->basePath() . '/app/Commands'])
                : null;
        }

        return $this->container->get($configKey);
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
     * @throws ReflectionException|BindingResolutionException
     */
    public function discoveryPath(): string
    {
        return $this->container->get('discovery_path');
    }

    public function setTheme(): void
    {
        /** @var AppConfig $config */
        $config = $this->config('app');

        /** @var class-string<ThemeInterface> $themeClass */
        $themeClass = $config->theme;

        if (! class_exists($themeClass)) {
            return;
        }

        $theme = new $themeClass();

        $filter = new ColorOutputFilter($theme);

        Component::setFilter($filter);
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
     * @param  array<string>  $argv
     *
     * @throws CommandNotFoundException|Throwable
     */
    public function runCommand(array $argv = []): int
    {
        $input = new CommandCall($argv);
        $shouldProfile = $input->hasFlag(GlobalFlag::PROFILE->value);
        $startTime = $shouldProfile ? (int) hrtime(true) : 0;
        $startMemory = $shouldProfile ? memory_get_usage(true) : 0;
        $commandName = '';
        $resultCode = null;

        try {
            $commandName = $this->resolveCommandName($input);
            $command = $this->commandRegistry->getCommand($commandName);

            if ($command === null) {
                if ($commandName === '' || $commandName === 'help') {
                    return $resultCode = $this->runHelp($input);
                }

                throw new CommandNotFoundException("Command '{$commandName}' not found.");
            }

            /** @var ExitCode $result */
            $result = $this->resolveExitCode(
                result: ($command->callable)($input, $this),
                commandName: $commandName,
            );

            return $resultCode = $result->value;
        } finally {
            if ($shouldProfile) {
                $this->renderProfile(
                    commandName: $commandName,
                    startTime: $startTime,
                    startMemory: $startMemory,
                    resultCode: $resultCode,
                );
            }
        }
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

    private function resolveCommandName(CommandCall $input): string
    {
        $commandName = $input->command;

        if ($input->subcommand !== null) {
            $commandName .= " {$input->subcommand}";
        }

        return $commandName;
    }

    private function runHelp(CommandCall $input): int
    {
        $helpCommand = $this->commandRegistry->getCommand('help');

        if ($helpCommand === null) {
            return ExitCode::Failure->value;
        }

        /** @var ExitCode $result */
        $result = $this->resolveExitCode(
            result: ($helpCommand->callable)($input, $this),
            commandName: 'help',
        );

        return $result->value;
    }

    private function resolveExitCode(mixed $result, string $commandName): ExitCode
    {
        if (! $result instanceof ExitCode) {
            throw new RuntimeException(
                "Command '{$commandName}' must return " . ExitCode::class . '.'
            );
        }

        return $result;
    }

    private function bindPaths(?string $appRoot): void
    {
        $appRoot ??= $this->appRoot();
        $discoveryPath = "{$appRoot}/.minicli/discovery";

        if (! is_dir($discoveryPath)) {
            mkdir($discoveryPath, 0775, true);
        }

        $this->container->bind('base_path', fn (): string => $appRoot);
        $this->container->bind('config_path', fn (): string => "{$appRoot}/config");
        $this->container->bind('logs_path', fn (): string => "{$appRoot}/logs");
        $this->container->bind('discovery_path', fn (): string => $discoveryPath);
    }

    private function findBinFileName(): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        // Index 0 is the current method, index 1 is the instantiation
        return basename($backtrace[1]['file'] ?? 'minicli');
    }

    private function configKey(string $name): string
    {
        return "config_{$name}";
    }

    private function renderProfile(string $commandName, int $startTime, int $startMemory, ?int $resultCode): void
    {
        $elapsedMilliseconds = (hrtime(true) - $startTime) / 1_000_000;
        $memoryDiff = max(0, memory_get_usage(true) - $startMemory);
        $peakMemory = memory_get_peak_usage(true);

        Divider::make()->fullWidth()->render();
        Text::make('Command Profile')->bold()->info()->render();

        $table = Table::make()->withBorders();
        $table->addRow(Row::make(['Metric', 'Value'])->bold());
        $table->addRow(Row::make(['Command', $commandName === '' ? 'help' : $commandName]));
        $table->addRow(Row::make(['Exit code', $resultCode === null ? 'exception' : (string) $resultCode]));
        $table->addRow(Row::make(['Time', formatElapsedTime($elapsedMilliseconds)]));
        $table->addRow(Row::make(['Memory delta', formatBytes($memoryDiff)]));
        $table->addRow(Row::make(['Peak memory', formatBytes($peakMemory)]));
        $table->render();
    }
}
