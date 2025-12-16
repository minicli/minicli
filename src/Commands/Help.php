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
        $config = $this->app->config('app');
        $this->info($config->name);

        return ExitCode::Success;
    }
}
