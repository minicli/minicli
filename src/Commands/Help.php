<?php

declare(strict_types=1);

namespace Minicli\Commands;

use Minicli\Attributes\Command;
use Minicli\Components\Divider;
use Minicli\Components\LineBreak;
use Minicli\Components\List\Item;
use Minicli\Components\List\ItemList;
use Minicli\Components\Text;
use Minicli\Config\AppConfig;
use Minicli\Console\CommandInfo;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;
use ReflectionClass;
use ReflectionFunction;

#[Command(description: 'List the available commands in your application')]
final class Help extends ConsoleCommand
{
    public function __invoke(): ExitCode
    {
        /** @var AppConfig $config */
        $config = $this->config('app');
        Text::make($config->name)->success()->render();
        LineBreak::make()->render();

        $commands = $this->app->commandRegistry->getCommandMap();
        ksort($commands);

        $parentCommands = [];
        $subcommands = [];

        foreach ($commands as $name => $commandInfo) {
            if (! str_contains($name, ' ')) {
                $parentCommands[$name] = $commandInfo;

                continue;
            }

            [$parent, $sub] = explode(' ', $name, 2);
            if (! isset($subcommands[$parent])) {
                $subcommands[$parent] = [];
            }
            $subcommands[$parent][$sub] = $commandInfo;
        }

        Text::make('Application Commands')->info()->bold()->render();
        $this->renderSection($parentCommands, $subcommands, $commands, CommandSection::Application);

        Divider::make()->fullWidth()->render();
        Text::make('miniCLI Commands')->info()->bold()->render();
        $this->renderSection($parentCommands, $subcommands, $commands, CommandSection::Minicli);

        $hasThirdPartyCommands = $this->sectionHasCommands(
            $parentCommands,
            $subcommands,
            $commands,
            CommandSection::ThirdParty,
        );

        if ($hasThirdPartyCommands) {
            Divider::make()->fullWidth()->render();
            Text::make('3rd-party Commands')->info()->bold()->render();
            $this->renderSection($parentCommands, $subcommands, $commands, CommandSection::ThirdParty);
        }

        return ExitCode::Success;
    }

    /**
     * @param  array<string, CommandInfo>  $parentCommands
     * @param  array<string, array<string, CommandInfo>>  $subcommands
     * @param  array<string, CommandInfo>  $commands
     */
    private function renderSection(
        array $parentCommands,
        array $subcommands,
        array $commands,
        CommandSection $targetSection,
    ): void {
        $list = ItemList::make();

        foreach ($parentCommands as $name => $commandInfo) {
            if ($this->resolveSection($name, $commandInfo, $subcommands, $commands) !== $targetSection) {
                continue;
            }

            $description = $commandInfo->description;

            if (! isset($subcommands[$name])) {
                $list->addItem(Item::make($name, $description));

                continue;
            }

            $defaultInfo = '';
            foreach ($subcommands[$name] as $subName => $subCommandInfo) {
                if ($subCommandInfo->callable === $commandInfo->callable) {
                    $defaultInfo = " (default: {$subName})";
                    break;
                }
            }

            $nestedList = ItemList::make();
            foreach ($subcommands[$name] as $subName => $subCommandInfo) {
                $nestedList->addItem(Item::make($subName, $subCommandInfo->description));
            }

            $fullDescription = $description . $defaultInfo;
            $list->addItem(
                Item::make($name, $fullDescription !== '' ? $fullDescription : null)
                    ->nested($nestedList)
            );
        }

        $list->render();
    }

    /**
     * @param  array<string, CommandInfo>  $parentCommands
     * @param  array<string, array<string, CommandInfo>>  $subcommands
     * @param  array<string, CommandInfo>  $commands
     */
    private function sectionHasCommands(array $parentCommands, array $subcommands, array $commands, CommandSection $section): bool
    {
        return array_any(
            $parentCommands,
            fn (CommandInfo $commandInfo, string $name): bool => $this->resolveSection($name, $commandInfo, $subcommands, $commands) === $section,
        );
    }

    /**
     * @param  array<string, array<string, CommandInfo>>  $subcommands
     * @param  array<string, CommandInfo>  $commands
     */
    private function resolveSection(
        string $parentCommandName,
        CommandInfo $parentCommandInfo,
        array $subcommands,
        array $commands,
    ): CommandSection {
        $originPath = $this->commandOriginPath($parentCommandInfo);

        if ($originPath === null && isset($subcommands[$parentCommandName])) {
            foreach (array_keys($subcommands[$parentCommandName]) as $subName) {
                $subcommand = $commands["{$parentCommandName} {$subName}"] ?? null;
                if (! $subcommand instanceof CommandInfo) {
                    continue;
                }

                $originPath = $this->commandOriginPath($subcommand);

                if ($originPath !== null) {
                    break;
                }
            }
        }

        if ($originPath === null) {
            return CommandSection::Application;
        }

        $basePath = $this->app->basePath();
        $minicliSourcePath = realpath(dirname(__DIR__));
        if ($minicliSourcePath !== false && str_starts_with($originPath, $minicliSourcePath)) {
            return CommandSection::Minicli;
        }

        if (str_starts_with($originPath, $basePath . '/vendor/')) {
            return CommandSection::ThirdParty;
        }

        return CommandSection::Application;
    }

    private function commandOriginPath(CommandInfo $commandInfo): ?string
    {
        $reflectionFunction = new ReflectionFunction($commandInfo->callable);
        $staticVariables = $reflectionFunction->getStaticVariables();
        $classReflection = $staticVariables['reflection'] ?? null;

        if (! $classReflection instanceof ReflectionClass) {
            return null;
        }

        $fileName = $classReflection->getFileName();

        return $fileName === false ? null : $fileName;
    }
}

enum CommandSection
{
    case Application;
    case Minicli;
    case ThirdParty;
}
