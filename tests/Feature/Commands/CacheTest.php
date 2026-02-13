<?php

declare(strict_types=1);

use Minicli\App;
use Minicli\Support\DiscoveryCache;

it('builds discovery cache with discovery cache', function (): void {
    $appRoot = makeCacheTempAppRoot();

    try {
        $app = new App($appRoot);

        ob_start();
        $result = $app->runCommand(['minicli', 'discovery', 'cache']);
        $output = (string) ob_get_clean();

        $cacheFile = "{$appRoot}/.minicli/discovery/" . DiscoveryCache::FILE_NAME;
        $contents = file_get_contents($cacheFile);

        expect($result)->toBe(0)
            ->and($output)->toContain('Cache built:')
            ->and(is_file($cacheFile))->toBeTrue()
            ->and($contents)->toBeString()
            ->and((string) $contents)->toContain('commands');
    } finally {
        removeCacheDirectory($appRoot);
    }
});

it('clears discovery cache with discovery clear', function (): void {
    $appRoot = makeCacheTempAppRoot();

    try {
        $app = new App($appRoot);
        $cacheFile = "{$appRoot}/.minicli/discovery/" . DiscoveryCache::FILE_NAME;

        $app->runCommand(['minicli', 'discovery', 'cache']);
        expect(is_file($cacheFile))->toBeTrue();

        ob_start();
        $result = $app->runCommand(['minicli', 'discovery', 'clear']);
        $output = (string) ob_get_clean();

        expect($result)->toBe(0)
            ->and($output)->toContain('Cache cleared:')
            ->and(is_file($cacheFile))->toBeFalse();
    } finally {
        removeCacheDirectory($appRoot);
    }
});

it('returns success when clearing cache that does not exist', function (): void {
    $appRoot = makeCacheTempAppRoot();

    try {
        $app = new App($appRoot);
        $cacheFile = "{$appRoot}/.minicli/discovery/" . DiscoveryCache::FILE_NAME;

        if (is_file($cacheFile)) {
            unlink($cacheFile);
        }

        ob_start();
        $result = $app->runCommand(['minicli', 'discovery', 'clear']);
        $output = (string) ob_get_clean();

        expect($result)->toBe(0)
            ->and($output)->toContain('Cache file not found:');
    } finally {
        removeCacheDirectory($appRoot);
    }
});

it('runs interactive default discovery command when subcommand is omitted', function (): void {
    $output = runInlinePhpWithInput(
        <<<'PHP'
$appRoot = sys_get_temp_dir() . '/minicli-discovery-default-' . uniqid('', true);
mkdir($appRoot . '/app/Commands', 0775, true);
mkdir($appRoot . '/app/Services', 0775, true);
mkdir($appRoot . '/config', 0775, true);
mkdir($appRoot . '/logs', 0775, true);

$app = new Minicli\App($appRoot);
$result = $app->runCommand(['minicli', 'discovery']);

echo "\nRESULT={$result}";
echo "\nCACHE_EXISTS=" . (is_file($appRoot . '/.minicli/discovery/minicli.json') ? 'yes' : 'no');
PHP,
        "1\n",
    );

    expect($output)->toContain('Select a discovery command')
        ->and($output)->toContain('RESULT=0')
        ->and($output)->toContain('CACHE_EXISTS=yes');
});

function makeCacheTempAppRoot(): string
{
    $appRoot = sys_get_temp_dir() . '/minicli-cache-command-' . bin2hex(random_bytes(8));

    mkdir("{$appRoot}/app/Commands", 0775, true);
    mkdir("{$appRoot}/app/Services", 0775, true);
    mkdir("{$appRoot}/config", 0775, true);
    mkdir("{$appRoot}/logs", 0775, true);

    return $appRoot;
}

function removeCacheDirectory(string $directory): void
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
            removeCacheDirectory($path);

            continue;
        }

        unlink($path);
    }

    rmdir($directory);
}
