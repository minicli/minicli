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

        $this->newline();
        $this->info('Available commands:');
        $this->newline();

        foreach ($commands as $name => $commandInfo) {
            $isSubcommand = str_contains($name, ' ');
            $padding = $isSubcommand ? "\t" : '';

            $description = $commandInfo->description !== ''
                ? " - {$commandInfo->description}"
                : '';

            $this->out("{$padding}{$name}{$description}");
        }

        $this->newline();

        return ExitCode::Success;
    }
}
