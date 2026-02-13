<?php

declare(strict_types=1);

use Minicli\App;

it('generates a command class with make command', function (): void {
    $appRoot = makeTempAppRoot();

    try {
        $app = new App($appRoot);

        ob_start();
        $result = $app->runCommand(['minicli', 'make', 'command', 'name=create-user']);
        $output = (string) ob_get_clean();

        $generatedFile = "{$appRoot}/app/Commands/CreateUser.php";

        expect($result)->toBe(0)
            ->and($output)->toContain('Created:')
            ->and(file_exists($generatedFile))->toBeTrue()
            ->and(file_get_contents($generatedFile))->toContain('namespace App\\Commands;')
            ->and(file_get_contents($generatedFile))->toContain('final class CreateUser extends ConsoleCommand')
            ->and(file_get_contents($generatedFile))->toContain("#[Command(description: 'CreateUser command description')]");
    } finally {
        removeDirectory($appRoot);
    }
});

it('generates a config class with make config', function (): void {
    $appRoot = makeTempAppRoot();

    try {
        $app = new App($appRoot);

        ob_start();
        $result = $app->runCommand(['minicli', 'make', 'config', 'name=api-client']);
        $output = (string) ob_get_clean();

        $generatedFile = "{$appRoot}/config/ApiClient.php";

        expect($result)->toBe(0)
            ->and($output)->toContain('Created:')
            ->and(file_exists($generatedFile))->toBeTrue()
            ->and(file_get_contents($generatedFile))->toContain("#[Config('api_client')]")
            ->and(file_get_contents($generatedFile))->toContain('final readonly class ApiClientConfig');
    } finally {
        removeDirectory($appRoot);
    }
});

it('generates a service class with make service', function (): void {
    $appRoot = makeTempAppRoot();

    try {
        $app = new App($appRoot);

        ob_start();
        $result = $app->runCommand(['minicli', 'make', 'service', 'name=user-notifier']);
        $output = (string) ob_get_clean();

        $generatedFile = "{$appRoot}/app/Services/UserNotifier.php";

        expect($result)->toBe(0)
            ->and($output)->toContain('Created:')
            ->and(file_exists($generatedFile))->toBeTrue()
            ->and(file_get_contents($generatedFile))->toContain('namespace App\\Services;')
            ->and(file_get_contents($generatedFile))->toContain("#[Service('user_notifier')]")
            ->and(file_get_contents($generatedFile))->toContain('class UserNotifier implements ServiceInterface');
    } finally {
        removeDirectory($appRoot);
    }
});

it('prompts for name when missing', function (): void {
    $output = runInlinePhpWithInput(
        <<<'PHP'
$appRoot = sys_get_temp_dir() . '/minicli-make-prompt-' . uniqid('', true);
mkdir($appRoot . '/app/Commands', 0775, true);
mkdir($appRoot . '/config', 0775, true);

$app = new Minicli\App($appRoot);
$result = $app->runCommand(['minicli', 'make', 'command']);

echo "\nRESULT={$result}";
echo "\nEXISTS=" . (file_exists($appRoot . '/app/Commands/FooBar.php') ? 'yes' : 'no');
PHP,
        "foo bar\n",
    );

    expect($output)->toContain('What should the command class be named?')
        ->and($output)->toContain('RESULT=0')
        ->and($output)->toContain('EXISTS=yes');
});

function makeTempAppRoot(): string
{
    $appRoot = sys_get_temp_dir() . '/minicli-make-' . bin2hex(random_bytes(8));

    mkdir("{$appRoot}/app/Commands", 0775, true);
    mkdir("{$appRoot}/app/Services", 0775, true);
    mkdir("{$appRoot}/config", 0775, true);

    return $appRoot;
}

function removeDirectory(string $directory): void
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
            removeDirectory($path);

            continue;
        }

        unlink($path);
    }

    rmdir($directory);
}
