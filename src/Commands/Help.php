<?php

declare(strict_types=1);

namespace Minicli\Commands;

use Minicli\Attributes\Command;
use Minicli\Components\LineBreak;
use Minicli\Components\List\ItemList;
use Minicli\Components\List\ListItem;
use Minicli\Components\Text;
use Minicli\Config\AppConfig;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

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

        // Group commands by parent
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

        Text::make('Available commands:')->info()->render();

        $list = ItemList::make();

        foreach ($parentCommands as $name => $commandInfo) {
            $description = $commandInfo->description;

            // Standalone command without subcommands
            if (! isset($subcommands[$name])) {
                $list->addItem(ListItem::make($name, $description));

                continue;
            }

            // Parent command with subcommands - find default
            $defaultInfo = '';
            foreach ($subcommands[$name] as $subName => $subCommandInfo) {
                if ($subCommandInfo->callable === $commandInfo->callable) {
                    $defaultInfo = " (default: {$subName})";
                    break;
                }
            }

            // Build nested list of subcommands
            $nestedList = ItemList::make();
            foreach ($subcommands[$name] as $subName => $subCommandInfo) {
                $subDescription = $subCommandInfo->description;
                $nestedList->addItem(ListItem::make($subName, $subDescription));
            }

            // Add parent with description and nested subcommands
            $fullDescription = $description . $defaultInfo;
            $list->addItem(
                ListItem::make($name, $fullDescription !== '' ? $fullDescription : null)
                    ->nested($nestedList)
            );
        }

        $list->render();

        return ExitCode::Success;
    }
}
