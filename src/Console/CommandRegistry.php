<?php

declare(strict_types=1);

namespace Minicli\Console;

use Minicli\App;
use Minicli\Attributes\Command;
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

        // Extract namespace
        if (! preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            return;
        }
        $namespace = $namespaceMatches[1];

        // Extract class name
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
            if (! $reflection->isSubclassOf(CommandController::class)) {
                return;
            }

            $this->registerCommandClass($reflection);
        } catch (ReflectionException) {
            // Skip classes that can't be reflected
        }
    }

    /**
     * @param  ReflectionClass<CommandController>  $reflection
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

        // Check if it has an __invoke method (single command)
        if ($reflection->hasMethod('__invoke')) {
            $closure = function (CommandCall $input, App $app) use ($reflection): mixed {
                /** @var CommandController $instance */
                $instance = $app->make($reflection->getName());
                $instance->boot($app, $input);
                assert(is_callable($instance));
                $result = $instance();
                $instance->teardown();

                return $result;
            };

            $commandInfo = new CommandInfo(
                callable: $closure,
                name: $classCommand->name,
                description: $classCommand->description
            );

            $this->registerCommand($classCommand->name, $commandInfo);
        }

        // Check for methods with Command attributes (sub-commands)
        $subcommands = [];
        $hasDefault = false;

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $methodAttributes = $method->getAttributes(Command::class);

            if (empty($methodAttributes)) {
                continue;
            }

            /** @var Command $methodCommand */
            $methodCommand = $methodAttributes[0]->newInstance();

            $closure = function (CommandCall $input, App $app) use ($reflection, $method): mixed {
                /** @var CommandController $instance */
                $instance = $app->make($reflection->getName());
                $instance->boot($app, $input);
                $result = $method->invoke($instance);
                $instance->teardown();

                return $result;
            };

            // Register the full command name
            $fullCommandName = "{$classCommand->name} {$methodCommand->name}";

            $commandInfo = new CommandInfo(
                callable: $closure,
                name: $fullCommandName,
                description: $methodCommand->description
            );

            $this->registerCommand($fullCommandName, $commandInfo);
            $subcommands[] = [
                'name' => $methodCommand->name,
                'description' => $methodCommand->description,
            ];

            // If this method is marked as default, also register it with just the class command name
            if ($methodCommand->default) {
                $defaultCommandInfo = new CommandInfo(
                    callable: $closure,
                    name: $classCommand->name,
                    description: $classCommand->description
                );

                $this->registerCommand($classCommand->name, $defaultCommandInfo);
                $hasDefault = true;
            }
        }

        // If there are subcommands but no default, register parent to show available subcommands
        if ($subcommands !== [] && ! $hasDefault) {
            $parentClosure = function (CommandCall $input, App $app) use ($classCommand, $subcommands): ExitCode {
                $app->error("Command '{$classCommand->name}' requires a subcommand.");
                $app->newline();
                $app->info('Available subcommands:');
                $app->newline();

                foreach ($subcommands as $subcommand) {
                    $description = $subcommand['description'] !== ''
                        ? " - {$subcommand['description']}"
                        : '';
                    $app->out("{$subcommand['name']}{$description}");
                    $app->newline();
                }

                return ExitCode::Invalid;
            };

            $parentCommandInfo = new CommandInfo(
                callable: $parentClosure,
                name: $classCommand->name,
                description: $classCommand->description
            );

            $this->registerCommand($classCommand->name, $parentCommandInfo);
        }
    }
}
