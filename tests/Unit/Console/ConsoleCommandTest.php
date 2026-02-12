<?php

declare(strict_types=1);

use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

it('boots console command with app and logger', function (): void {
    $command = new ConsoleCommandFixture();
    $command->boot(getConfiguredApp());

    expect($command->hasApp())->toBeTrue()
        ->and($command->hasLogger())->toBeTrue()
        ->and($command->readConfigName())->toBe('Configured Test App');
});

final class ConsoleCommandFixture extends ConsoleCommand
{
    public function __invoke(): ExitCode
    {
        return ExitCode::Success;
    }

    public function hasApp(): bool
    {
        return isset($this->app);
    }

    public function hasLogger(): bool
    {
        return isset($this->logger);
    }

    public function readConfigName(): ?string
    {
        $config = $this->config('app');

        return is_object($config) && isset($config->name)
            ? (string) $config->name
            : null;
    }
}
