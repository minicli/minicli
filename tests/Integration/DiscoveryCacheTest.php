<?php

declare(strict_types=1);

use Minicli\App;

it('writes discovery cache file during boot', function (): void {
    $app = getConfiguredApp();
    $cacheFile = getFixtureAppRoot() . '/.minicli/discovery/minicli.json';

    expect($app->commandRegistry->getCommand('help'))->not->toBeNull()
        ->and(is_file($cacheFile))->toBeTrue();
});

it('refreshes command discovery when new files are added', function (): void {
    $appRoot = makeDiscoveryTempAppRoot();

    try {
        writeFixtureCommand($appRoot, 'Ping');

        $firstApp = new App($appRoot);
        expect($firstApp->commandRegistry->getCommand('ping'))->not->toBeNull();

        $cacheFile = "{$appRoot}/.minicli/discovery/minicli.json";
        expect(is_file($cacheFile))->toBeTrue();

        writeFixtureCommand($appRoot, 'Pong');

        $secondApp = new App($appRoot);
        expect($secondApp->commandRegistry->getCommand('ping'))->not->toBeNull()
            ->and($secondApp->commandRegistry->getCommand('pong'))->not->toBeNull();
    } finally {
        removeDiscoveryDirectory($appRoot);
    }
});

function makeDiscoveryTempAppRoot(): string
{
    $appRoot = sys_get_temp_dir() . '/minicli-discovery-' . bin2hex(random_bytes(8));

    mkdir("{$appRoot}/app/Commands", 0775, true);
    mkdir("{$appRoot}/app/Services", 0775, true);
    mkdir("{$appRoot}/config", 0775, true);
    mkdir("{$appRoot}/logs", 0775, true);

    return $appRoot;
}

function writeFixtureCommand(string $appRoot, string $name): void
{
    $path = "{$appRoot}/app/Commands/{$name}Command.php";
    $command = strtolower($name);

    file_put_contents($path, <<<PHP
<?php

declare(strict_types=1);

namespace App\Commands;

use Minicli\Attributes\Command;
use Minicli\Console\ConsoleCommand;
use Minicli\Console\ExitCode;

#[Command(name: '{$command}', description: '{$name} command')]
final class {$name}Command extends ConsoleCommand
{
    public function __invoke(): ExitCode
    {
        return ExitCode::Success;
    }
}
PHP);
}

function removeDiscoveryDirectory(string $directory): void
{
    if (! is_dir($directory)) {
        return;
    }

    $entries = scandir($directory);
    if ($entries === false) {
        return;
    }

    foreach ($entries as $entry) {
        if ($entry === '.') {
            continue;
        }
        if ($entry === '..') {
            continue;
        }

        $path = "{$directory}/{$entry}";
        if (is_dir($path)) {
            removeDiscoveryDirectory($path);

            continue;
        }

        unlink($path);
    }

    rmdir($directory);
}
