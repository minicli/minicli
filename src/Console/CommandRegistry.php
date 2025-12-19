<?php

declare(strict_types=1);

namespace Minicli\Console;

use Closure;
use Minicli\App;
use Minicli\Attributes\Command;
use Minicli\Components\LineBreak;
use Minicli\Components\Text;
use Minicli\Config\AppConfig;
use Minicli\Contracts\ServiceInterface;
use Minicli\Exceptions\BindingResolutionException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

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
        $commandSources = $this->buildCommandSources($app);

        $this->registerDefaultCommands();
        $this->registerCommandsFromSources($commandSources);
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

    private function registerDefaultCommands(): void
    {
        $commandsPath = dirname(__DIR__) . '/Commands';
        if (is_dir($commandsPath)) {
            $this->scanAndRegisterCommands($commandsPath);
        }
    }

    /**
     * @param  array<string>  $sources
     */
    private function registerCommandsFromSources(array $sources): void
    {
        foreach ($sources as $source) {
            if (is_dir($source)) {
                $this->scanAndRegisterCommands($source);
            }
        }
    }

    private function scanAndRegisterCommands(string $path): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $this->loadAndRegisterCommandsFromFile($file->getPathname());
            }
        }
    }

    private function loadAndRegisterCommandsFromFile(string $filePath): void
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return;
        }

        if (! preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            return;
        }
        $namespace = $namespaceMatches[1];

        if (! preg_match('/class\s+(\w+)/', $content, $classMatches)) {
            return;
        }

        $className = $classMatches[1];
        $fullName = $namespace . '\\' . $className;

        try {
            if (! class_exists($fullName)) {
                return;
            }

            $reflection = new ReflectionClass($fullName);
            if (! $reflection->isSubclassOf(ConsoleCommand::class)) {
                return;
            }

            $this->registerCommandClass($reflection);
        } catch (ReflectionException) {
            // Skip classes that can't be reflected
        }
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

        $this->registerInvokeCommand($reflection, $commandName, $classCommand);
        $result = $this->registerSubcommands($reflection, $commandName, $classCommand);

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
     *
     * @throws ReflectionException
     */
    private function registerInvokeCommand(ReflectionClass $reflection, string $commandName, Command $classCommand): void
    {
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
            $argumentsInfo
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
     * @return array{subcommands: array<CommandRegisterInfo|null>, hasDefault: bool}
     *
     * @throws ReflectionException
     */
    private function registerSubcommands(ReflectionClass $reflection, string $commandName, Command $classCommand): array
    {
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
            $argumentsHandler = new ArgumentsHandler($method->getParameters());
            $argumentsInfo = $argumentsHandler->extractArgumentInfo();

            $subcommands[] = $this->registerSubcommand(
                $reflection,
                $method,
                $commandName,
                $methodCommand->description,
                $argumentsHandler,
                $argumentsInfo,
                $subcommandName
            );

            if ($methodCommand->default) {
                $this->registerSubcommand(
                    $reflection,
                    $method,
                    $commandName,
                    $classCommand->description,
                    $argumentsHandler,
                    $argumentsInfo
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
            Text::make("Command '{$commandName}' requires a subcommand.")->error()->render();
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
     */
    private function registerSubcommand(
        ReflectionClass $reflection,
        ReflectionMethod $method,
        string $commandName,
        string $description,
        ArgumentsHandler $argumentsHandler,
        array $argumentsInfo,
        ?string $subcommandName = null
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
            $argumentsInfo
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
     */
    private function createCommandExecutionClosure(
        ReflectionClass $reflection,
        ReflectionMethod $method,
        string $commandName,
        string $description,
        ArgumentsHandler $argumentsHandler,
        array $argumentsInfo
    ): Closure {
        return function (CommandCall $input, App $app) use ($reflection, $method, $commandName, $description, $argumentsHandler, $argumentsInfo): mixed {
            if ($input->hasFlag(GlobalFlag::HELP->value)) {
                $tempCommandInfo = new CommandInfo(
                    callable: fn (): ExitCode => ExitCode::Success,
                    name: $commandName,
                    description: $description,
                    arguments: $argumentsInfo
                );

                return $tempCommandInfo->displayHelp($app);
            }

            /** @var ConsoleCommand $instance */
            $instance = $app->make($reflection->getName());
            $instance->boot($app);

            if ($input->hasFlag(GlobalFlag::QUIET->value)) {
                $instance->setQuiet(true);
            }

            $arguments = $argumentsHandler->prepareArguments($input);
            $result = $method->invokeArgs($instance, $arguments);
            $instance->teardown();

            return $result;
        };
    }
}
