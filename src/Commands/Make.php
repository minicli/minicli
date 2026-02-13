<?php

declare(strict_types=1);

namespace Minicli\Commands;

use Minicli\Attributes\Command;
use Minicli\Components\Alert;
use Minicli\Components\Question;
use Minicli\Components\Text;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;
use RuntimeException;

#[Command(description: 'Generate classes in your application')]
final class Make extends ConsoleCommand
{
    #[Command(description: 'Generate a new command class')]
    public function command(?string $name = null): ExitCode
    {
        $className = $this->resolveClassName($name, 'command class');
        if ($className === null) {
            return ExitCode::Invalid;
        }

        return $this->generateFile(
            className: $className,
            directory: $this->app->basePath() . '/app/Commands',
            stubPath: dirname(__DIR__) . '/Stubs/Make/command.stub',
            replacements: [
                '{{NAME}}' => $className,
            ],
        );
    }

    #[Command(description: 'Generate a new config class')]
    public function config(?string $name = null): ExitCode
    {
        $className = $this->resolveClassName($name, 'config class');
        if ($className === null) {
            return ExitCode::Invalid;
        }

        return $this->generateFile(
            className: $className,
            directory: $this->app->configPath(),
            stubPath: dirname(__DIR__) . '/Stubs/Make/config.stub',
            replacements: [
                '{{NAME}}' => $className,
                '{{CONFIG_KEY}}' => toSnakeCase($className),
            ],
        );
    }

    #[Command(description: 'Generate a new service class')]
    public function service(?string $name = null): ExitCode
    {
        $className = $this->resolveClassName($name, 'service class');
        if ($className === null) {
            return ExitCode::Invalid;
        }

        return $this->generateFile(
            className: $className,
            directory: $this->app->basePath() . '/app/Services',
            stubPath: dirname(__DIR__) . '/Stubs/Make/service.stub',
            replacements: [
                '{{NAME}}' => $className,
                '{{SERVICE_KEY}}' => toSnakeCase($className),
            ],
        );
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function generateFile(
        string $className,
        string $directory,
        string $stubPath,
        array $replacements,
    ): ExitCode {
        $targetFile = "{$directory}/{$className}.php";

        if (file_exists($targetFile)) {
            Alert::make("File already exists: {$targetFile}")->error()->render();

            return ExitCode::Invalid;
        }

        if (! is_dir($directory) && (! mkdir($directory, 0775, true) && ! is_dir($directory))) {
            throw new RuntimeException("Unable to create directory: {$directory}");
        }

        $stub = file_get_contents($stubPath);
        if ($stub === false) {
            throw new RuntimeException("Unable to read stub file: {$stubPath}");
        }

        $content = str_replace(array_keys($replacements), array_values($replacements), $stub);

        if (file_put_contents($targetFile, $content) === false) {
            throw new RuntimeException("Unable to write file: {$targetFile}");
        }

        Text::make("Created: {$targetFile}")->success()->render();

        return ExitCode::Success;
    }

    private function resolveClassName(?string $name, string $class): ?string
    {
        $rawName = $name;

        if ($rawName === null) {
            $answer = Question::make("What should the {$class} be named?")->ask();
            $rawName = is_string($answer) ? $answer : '';
        }

        $className = toPascalCase($rawName);

        if ($className === '') {
            Alert::make('Please provide a valid class name.')->error()->render();

            return null;
        }

        return $className;
    }
}
