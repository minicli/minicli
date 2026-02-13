<?php

declare(strict_types=1);

namespace Minicli\Console;

use Closure;
use Minicli\App;
use Minicli\Attributes\Command;
use Minicli\Attributes\Middleware;
use Minicli\Components\Alert;
use Minicli\Components\Component;
use Minicli\Components\LineBreak;
use Minicli\Components\Text;
use Minicli\Config\AppConfig;
use Minicli\Contracts\MiddlewareInterface;
use Minicli\Contracts\ServiceInterface;
use Minicli\Exceptions\BindingResolutionException;
use Minicli\Support\DiscoveryCache;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

final class CommandRegistry implements ServiceInterface
{
    /**
     * @param  array<string, CommandInfo>  $defaultRegistry
     */
    public function __construct(private array $defaultRegistry = []) {}

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function load(App $app): void
    {
        $cache = new DiscoveryCache($app);
        $commandSources = $this->buildCommandSources($app);

        $this->registerDefaultCommands($cache);
        $this->registerCommandsFromSources($commandSources, $cache);
    }

    public function registerCommand(string $name, CommandInfo $commandInfo): void
    {
        $this->defaultRegistry[$name] = $commandInfo;
    }

    public function getCommand(string $command): ?CommandInfo
    {
        return $this->defaultRegistry[$command] ?? null;
    }

    /**
     * @return array<string, CommandInfo>
     */
    public function getCommandMap(): array
    {
        return $this->defaultRegistry;
    }

    /**
     * @return array<string>
     *
     * @throws BindingResolutionException|ReflectionException
     */
    private function buildCommandSources(App $app): array
    {
        /** @var AppConfig $config */
        $config = $app->config('app');

        $commandsPath = $config->commandPaths;
        $commandSources = [];
        foreach ($commandsPath as $path) {
            if (str_starts_with((string) $path, '@')) {
                $path = str_replace('@', $app->basePath() . '/vendor/', $path) . '/Commands';
            }
            $commandSources[] = $path;
        }

        return $commandSources;
    }

    private function registerDefaultCommands(DiscoveryCache $cache): void
    {
        $commandsPath = dirname(__DIR__) . '/Commands';
        if (is_dir($commandsPath)) {
            $this->scanAndRegisterCommands($commandsPath, $cache);
        }
    }

    /**
     * @param  array<string>  $sources
     */
    private function registerCommandsFromSources(array $sources, DiscoveryCache $cache): void
    {
        foreach ($sources as $source) {
            if (is_dir($source)) {
                $this->scanAndRegisterCommands($source, $cache);
            }
        }
    }

    private function scanAndRegisterCommands(string $path, DiscoveryCache $cache): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $this->loadAndRegisterCommandsFromFile($file->getPathname(), $cache);
            }
        }
    }

    private function loadAndRegisterCommandsFromFile(string $filePath, DiscoveryCache $cache): void
    {
        $classes = $cache->rememberFile(
            domain: 'commands',
            filePath: $filePath,
            resolver: fn (string $realPath): array => $this->classesDefinedInFile($realPath),
        );

        foreach ($classes as $className) {
            if (! class_exists($className)) {
                require_once $filePath;
            }

            if (! class_exists($className)) {
                continue;
            }

            /** @var class-string $className */
            $reflection = new ReflectionClass($className);
            if (! $reflection->isSubclassOf(ConsoleCommand::class)) {
                continue;
            }

            $this->registerCommandClass($reflection);
        }
    }

    /**
     * @return array<string>
     */
    private function classesDefinedInFile(string $filePath): array
    {
        $realPath = realpath($filePath);
        if ($realPath === false) {
            return [];
        }

        require_once $realPath;

        $classes = [];

        foreach (get_declared_classes() as $className) {
            if (! class_exists($className)) {
                continue;
            }

            /** @var class-string $className */
            $reflection = new ReflectionClass($className);

            if ($reflection->getFileName() === $realPath) {
                $classes[] = $className;
            }
        }

        return $classes;
    }

    /**
     * @param  ReflectionClass<ConsoleCommand>  $reflection
     *
     * @throws ReflectionException
     */
    private function registerCommandClass(ReflectionClass $reflection): void
    {
        $classAttributes = $reflection->getAttributes(Command::class);

        if ($classAttributes === []) {
            return;
        }

        /** @var Command $classCommand */
        $classCommand = $classAttributes[0]->newInstance();
        $commandName = $this->generateCommandName($classCommand->name, $reflection->getShortName());
        $classMiddlewares = $this->extractMiddlewares($reflection->getAttributes(Middleware::class));

        $this->registerInvokeCommand($reflection, $commandName, $classCommand, $classMiddlewares);
        $result = $this->registerSubcommands($reflection, $commandName, $classCommand, $classMiddlewares);

        if ($result['subcommands'] !== [] && ! $result['hasDefault']) {
            $this->registerParentCommandWithSubcommands(
                $commandName,
                $classCommand->description,
                $result['subcommands']
            );
        }
    }

    private function generateCommandName(string $providedName, string $className): string
    {
        if ($providedName !== '') {
            return $providedName;
        }

        /** @var string $cleanedName */
        $cleanedName = preg_replace('/Command$/', '', $className);

        return toKebabCase($cleanedName);
    }

    /**
     * @param  ReflectionClass<ConsoleCommand>  $reflection
     * @param  array<class-string>  $middlewares
     *
     * @throws ReflectionException
     */
    private function registerInvokeCommand(
        ReflectionClass $reflection,
        string $commandName,
        Command $classCommand,
        array $middlewares = [],
    ): void {
        if (! $reflection->hasMethod('__invoke')) {
            return;
        }

        $invokeMethod = $reflection->getMethod('__invoke');
        $argumentsHandler = new ArgumentsHandler($invokeMethod->getParameters());
        $argumentsInfo = $argumentsHandler->extractArgumentInfo();

        $closure = $this->createCommandExecutionClosure(
            $reflection,
            $invokeMethod,
            $commandName,
            $classCommand->description,
            $argumentsHandler,
            $argumentsInfo,
            $middlewares,
        );

        $commandInfo = new CommandInfo(
            callable: $closure,
            name: $commandName,
            description: $classCommand->description,
            arguments: $argumentsInfo
        );

        $this->registerCommand($commandName, $commandInfo);
    }

    /**
     * @param  ReflectionClass<ConsoleCommand>  $reflection
     * @param  array<class-string>  $classMiddlewares
     * @return array{subcommands: array<CommandRegisterInfo|null>, hasDefault: bool}
     *
     * @throws ReflectionException
     */
    private function registerSubcommands(
        ReflectionClass $reflection,
        string $commandName,
        Command $classCommand,
        array $classMiddlewares = [],
    ): array {
        $subcommands = [];
        $hasDefault = false;

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methodAttributes = $method->getAttributes(Command::class);

            if (empty($methodAttributes)) {
                continue;
            }

            /** @var Command $methodCommand */
            $methodCommand = $methodAttributes[0]->newInstance();
            $subcommandName = $this->generateCommandName($methodCommand->name, $method->getName());
            $methodMiddlewares = $this->extractMiddlewares($method->getAttributes(Middleware::class));
            $middlewares = [...$classMiddlewares, ...$methodMiddlewares];
            $argumentsHandler = new ArgumentsHandler($method->getParameters());
            $argumentsInfo = $argumentsHandler->extractArgumentInfo();

            $subcommands[] = $this->registerSubcommand(
                $reflection,
                $method,
                $commandName,
                $methodCommand->description,
                $argumentsHandler,
                $argumentsInfo,
                $subcommandName,
                $middlewares,
            );

            if ($methodCommand->default) {
                $this->registerSubcommand(
                    $reflection,
                    $method,
                    $commandName,
                    $classCommand->description,
                    $argumentsHandler,
                    $argumentsInfo,
                    middlewares: $middlewares,
                );
                $hasDefault = true;
            }
        }

        return [
            'subcommands' => $subcommands,
            'hasDefault' => $hasDefault,
        ];
    }

    /**
     * @param  array<CommandRegisterInfo|null>  $subcommands
     */
    private function registerParentCommandWithSubcommands(string $commandName, string $description, array $subcommands): void
    {
        $parentClosure = function (CommandCall $input, App $app) use ($commandName, $subcommands): ExitCode {
            Alert::make("Command '{$commandName}' requires a subcommand.")->error()->render();
            LineBreak::make()->render();
            Text::make('Available subcommands:')->info()->render();
            LineBreak::make()->render();

            foreach ($subcommands as $subcommand) {
                if ($subcommand === null) {
                    continue;
                }

                $description = $subcommand->description !== ''
                    ? " - {$subcommand->description}"
                    : '';
                Text::make("{$subcommand->name}{$description}")->render();
            }

            return ExitCode::Invalid;
        };

        $parentCommandInfo = new CommandInfo(
            callable: $parentClosure,
            name: $commandName,
            description: $description
        );

        $this->registerCommand($commandName, $parentCommandInfo);
    }

    /**
     * @param  ReflectionClass<ConsoleCommand>  $reflection
     * @param  array<ArgumentInfo>  $argumentsInfo
     * @param  array<class-string>  $middlewares
     */
    private function registerSubcommand(
        ReflectionClass $reflection,
        ReflectionMethod $method,
        string $commandName,
        string $description,
        ArgumentsHandler $argumentsHandler,
        array $argumentsInfo,
        ?string $subcommandName = null,
        array $middlewares = [],
    ): ?CommandRegisterInfo {
        $fullCommandName = $subcommandName !== null
            ? "{$commandName} {$subcommandName}"
            : $commandName;

        $closure = $this->createCommandExecutionClosure(
            $reflection,
            $method,
            $fullCommandName,
            $description,
            $argumentsHandler,
            $argumentsInfo,
            $middlewares,
        );

        $commandInfo = new CommandInfo(
            callable: $closure,
            name: $fullCommandName,
            description: $description,
            arguments: $argumentsInfo
        );

        $this->registerCommand($fullCommandName, $commandInfo);

        return $subcommandName !== null
            ? new CommandRegisterInfo(name: $subcommandName, description: $description)
            : null;
    }

    /**
     * @param  ReflectionClass<ConsoleCommand>  $reflection
     * @param  array<ArgumentInfo>  $argumentsInfo
     * @param  array<class-string>  $middlewares
     */
    private function createCommandExecutionClosure(
        ReflectionClass $reflection,
        ReflectionMethod $method,
        string $commandName,
        string $description,
        ArgumentsHandler $argumentsHandler,
        array $argumentsInfo,
        array $middlewares = [],
    ): Closure {
        return function (CommandCall $input, App $app) use ($reflection, $method, $commandName, $description, $argumentsHandler, $argumentsInfo, $middlewares): ExitCode {
            if ($input->hasFlag(GlobalFlag::HELP->value)) {
                $tempCommandInfo = new CommandInfo(
                    callable: fn (): ExitCode => ExitCode::Success,
                    name: $commandName,
                    description: $description,
                    arguments: $argumentsInfo
                );

                return $tempCommandInfo->displayHelp();
            }

            /** @var ConsoleCommand $instance */
            $instance = $app->make($reflection->getName());
            $instance->boot($app);

            if ($input->hasFlag(GlobalFlag::QUIET->value)) {
                Component::setQuiet(true);
            }

            $destination = function (CommandCall $input, App $app) use ($argumentsHandler, $method, $instance, $commandName): ExitCode {
                $arguments = $argumentsHandler->prepareArguments($input);
                $result = $method->invokeArgs($instance, $arguments);

                if (! $result instanceof ExitCode) {
                    throw new RuntimeException(
                        "Command '{$commandName}' must return " . ExitCode::class . '.'
                    );
                }

                return $result;
            };

            try {
                return $this->runMiddlewarePipeline($middlewares, $input, $app, $destination);
            } finally {
                try {
                    $instance->teardown();
                } finally {
                    // Reset quiet flag after command execution
                    Component::setQuiet(false);
                }
            }
        };
    }

    /**
     * @param  array<ReflectionAttribute<Middleware>>  $attributes
     * @return array<class-string>
     */
    private function extractMiddlewares(array $attributes): array
    {
        if ($attributes === []) {
            return [];
        }

        /** @var Middleware $middlewareAttribute */
        $middlewareAttribute = $attributes[0]->newInstance();

        return $middlewareAttribute->middlewares;
    }

    /**
     * @param  array<class-string>  $middlewares
     * @param  Closure(CommandCall, App): ExitCode  $destination
     */
    private function runMiddlewarePipeline(
        array $middlewares,
        CommandCall $input,
        App $app,
        Closure $destination,
    ): ExitCode {
        $pipeline = array_reduce(
            array_reverse($middlewares),
            fn (Closure $next, string $middlewareClass): Closure => function (CommandCall $input, App $application) use ($next, $middlewareClass, $app): ExitCode {
                $middleware = $app->make($middlewareClass);

                if (! $middleware instanceof MiddlewareInterface) {
                    throw new RuntimeException(
                        "Middleware '{$middlewareClass}' must implement " . MiddlewareInterface::class . '.'
                    );
                }

                return $middleware->handle($input, $application, $next);
            },
            $destination,
        );

        return $pipeline($input, $app);
    }
}
