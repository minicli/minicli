<?php

declare(strict_types=1);

namespace Minicli\Commands;

use Minicli\Attributes\Command;
use Minicli\Config\AppConfig;
use Minicli\Console\CommandController;
use Minicli\Console\ExitCode;

#[Command(name: 'help', description: 'List the available commands in your application')]
final class Help extends CommandController
{
    public function __invoke(): ExitCode
    {
        /** @var AppConfig $config */
        $config = $this->config('app');
        $this->success($config->name);

        $commands = $this->app->commandRegistry->getCommandMap();
        ksort($commands);

        // Identify parent commands and their defaults
        $parentCommands = [];
        foreach ($commands as $name => $commandInfo) {
            if (str_contains($name, ' ')) {
                [$parent, $sub] = explode(' ', $name, 2);
                if (! isset($parentCommands[$parent])) {
                    $parentCommands[$parent] = [];
                }
                $parentCommands[$parent][] = $sub;
            }
        }

        $this->newline();
        $this->info('Available commands:');
        $this->newline();

        foreach ($commands as $name => $commandInfo) {
            $isSubcommand = str_contains($name, ' ');

            // Extract just the subcommand name if this is a subcommand
            $displayName = $name;
            if ($isSubcommand) {
                $parts = explode(' ', $name, 2);
                $displayName = $parts[1];
            }

            $padding = $isSubcommand ? "\t" : '';

            // Check if this is a parent command with a default
            $defaultInfo = '';
            if (! $isSubcommand && isset($parentCommands[$name])) {
                foreach ($parentCommands[$name] as $subName) {
                    $fullSubName = "{$name} {$subName}";
                    if (isset($commands[$fullSubName]) && $commands[$fullSubName]->callable === $commandInfo->callable) {
                        $defaultInfo = " (default: {$subName})";
                        break;
                    }
                }
            }

            $description = $commandInfo->description !== ''
                ? " - {$commandInfo->description}"
                : '';

            $this->out("{$padding}{$displayName}{$description}{$defaultInfo}");
            $this->newline();
        }

        return ExitCode::Success;
    }
}
