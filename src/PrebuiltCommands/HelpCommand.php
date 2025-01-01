<?php

declare(strict_types=1);

namespace Minicli\PrebuiltCommands;

use Minicli\App;

class HelpCommand implements IsRegisterableCommand
{
    protected $customCode = null;
    protected array $customCodeArgs = [];

    protected array $knownCommands = [
        ['help', '', 'Lists the available commands.'],
    ];

    public function __construct(private App $app)
    {
        $this->register();
    }

    public function register(): void
    {
        $app = $this->app;
        $app->registerCommand('help', function () use ($app) {
            $app->success($app->me . ' [required] <optional> - Description' , false);

            $this->formatCommands($this->knownCommands);

            if (is_callable($this->customCode)) {
                call_user_func($this->customCode, ...$this->customCodeArgs);
            }
        });
    }

    protected function formatCommands(array $commands)
    {
        $formattedCommands = array_map(function ($item) {
            [$name, $signature, $description] = $item;
            return [$signature ? "$name $signature" : $name, $description];
        }, $commands);

        $maxLen = max(array_map('strlen', array_column($formattedCommands, 0)));
        $dashColumn = min(42, $maxLen + 2);

        foreach ($formattedCommands as [$command, $description]) {
            if ($description === null) {
                echo $command . "\n";
            } elseif (strlen($command) > 40) {
                echo $command . "\n";
                echo str_repeat(' ', 4) . '— ' . $description . "\n";
            } else {
                $spaces = max(1, $dashColumn - strlen($command) - 1);
                echo $command . str_repeat(' ', $spaces) . '— ' . $description . "\n";
            }
        }
    }

    public function registerCallback(callable $customCode, array $customCodeArgs = [])
    {
        $this->customCode = $customCode;
        $this->customCodeArgs = $customCodeArgs;
    }

    public function addCommandListing(string $name, string $signature, ?string $description = null)
    {
        $this->knownCommands[] = [$name, $signature, $description];
    }
}
