<?php

declare(strict_types=1);

namespace Minicli\Console;

use BackedEnum;
use Exception;
use Minicli\App;
use Minicli\Attributes\Command;
use Minicli\Config\AppConfig;
use Minicli\Contracts\ServiceInterface;
use Minicli\Exceptions\BindingResolutionException;
use Minicli\Exceptions\CastException;
use Minicli\Exceptions\MissingParametersException;
use Minicli\Input\InputCaster;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use UnitEnum;

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

        // Auto-generate command name if not provided
        $commandName = $classCommand->name;
        if ($commandName === '') {
            $className = $reflection->getShortName();
            /** @var string $className */
            $className = preg_replace('/Command$/', '', $className);
            $commandName = toKebabCase($className);
        }

        // Check if it has an __invoke method (single command)
        if ($reflection->hasMethod('__invoke')) {
            $invokeMethod = $reflection->getMethod('__invoke');
            $closure = function (CommandCall $input, App $app) use ($reflection, $invokeMethod): mixed {
                /** @var CommandController $instance */
                $instance = $app->make($reflection->getName());
                $instance->boot($app, $input);

                $arguments = $this->prepareArguments($invokeMethod->getParameters(), $input);
                $result = $invokeMethod->invokeArgs($instance, $arguments);
                $instance->teardown();

                return $result;
            };

            $commandInfo = new CommandInfo(
                callable: $closure,
                name: $commandName,
                description: $classCommand->description
            );

            $this->registerCommand($commandName, $commandInfo);
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

            // Auto-generate subcommand name if not provided
            $subcommandName = $methodCommand->name;
            if ($subcommandName === '') {
                $subcommandName = toKebabCase($method->getName());
            }

            $closure = function (CommandCall $input, App $app) use ($reflection, $method): mixed {
                /** @var CommandController $instance */
                $instance = $app->make($reflection->getName());
                $instance->boot($app, $input);

                $arguments = $this->prepareArguments($method->getParameters(), $input);
                $result = $method->invokeArgs($instance, $arguments);
                $instance->teardown();

                return $result;
            };

            // Register the full command name
            $fullCommandName = "{$commandName} {$subcommandName}";

            $commandInfo = new CommandInfo(
                callable: $closure,
                name: $fullCommandName,
                description: $methodCommand->description
            );

            $this->registerCommand($fullCommandName, $commandInfo);
            $subcommands[] = [
                'name' => $subcommandName,
                'description' => $methodCommand->description,
            ];

            // If this method is marked as default, also register it with just the class command name
            if ($methodCommand->default) {
                // Create a separate closure for the default command to ensure proper binding
                $defaultClosure = function (CommandCall $input, App $app) use ($reflection, $method): mixed {
                    /** @var CommandController $instance */
                    $instance = $app->make($reflection->getName());
                    $instance->boot($app, $input);

                    $arguments = $this->prepareArguments($method->getParameters(), $input);
                    $result = $method->invokeArgs($instance, $arguments);
                    $instance->teardown();

                    return $result;
                };

                $defaultCommandInfo = new CommandInfo(
                    callable: $defaultClosure,
                    name: $commandName,
                    description: $classCommand->description
                );

                $this->registerCommand($commandName, $defaultCommandInfo);
                $hasDefault = true;
            }
        }

        // If there are subcommands but no default, register parent to show available subcommands
        if ($subcommands !== [] && ! $hasDefault) {
            $parentClosure = function (CommandCall $input, App $app) use ($commandName, $subcommands): ExitCode {
                $app->error("Command '{$commandName}' requires a subcommand.");
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
                name: $commandName,
                description: $classCommand->description
            );

            $this->registerCommand($commandName, $parentCommandInfo);
        }
    }

    /**
     * Prepare arguments from CommandCall based on method parameters
     *
     * @param  array<ReflectionParameter>  $parameters
     * @return array<mixed>
     *
     * @throws MissingParametersException|CastException|ReflectionException
     */
    private function prepareArguments(array $parameters, CommandCall $input): array
    {
        $arguments = [];
        $missing = [];

        foreach ($parameters as $parameter) {
            $paramName = $parameter->getName();
            $kebabName = toKebabCase($paramName);
            $type = $parameter->getType();

            if (! $type instanceof ReflectionNamedType) {
                continue;
            }

            $typeName = $type->getName();
            $isNullable = $type->allowsNull();
            $hasDefault = $parameter->isDefaultValueAvailable();

            // Handle boolean parameters as flags
            if ($typeName === 'bool') {
                $arguments[] = $input->hasFlag($kebabName);

                continue;
            }

            $hasValue = $input->hasParam($kebabName);
            if (! $hasValue && ! $isNullable && ! $hasDefault) {
                $missing[] = $kebabName;

                continue;
            }

            // If parameter is missing but optional, use default or null
            if (! $hasValue) {
                $arguments[] = $hasDefault
                    ? $parameter->getDefaultValue()
                    : null;

                continue;
            }

            $value = $input->getParam($kebabName);
            try {
                $arguments[] = $this->castValue($value, $typeName);
            } catch (Exception) {
                throw new CastException($kebabName);
            }
        }

        if ($missing !== []) {
            throw new MissingParametersException($missing);
        }

        return $arguments;
    }

    /**
     * @throws CastException
     */
    private function castValue(?string $value, string $typeName): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($typeName) {
            'string' => $value,
            'int' => InputCaster::castToInteger($value),
            'float' => InputCaster::castToFloat($value),
            'array' => InputCaster::castToArray($value),
            default => $this->castToEnumOrDefault($value, $typeName),
        };
    }

    /**
     * @throws CastException
     */
    private function castToEnumOrDefault(string $value, string $typeName): mixed
    {
        if (is_subclass_of($typeName, UnitEnum::class) || is_subclass_of($typeName, BackedEnum::class)) {
            return InputCaster::castToEnum($value, $typeName);
        }

        // For other types, return as-is (string)
        return $value;
    }
}
