<?php

declare(strict_types=1);

it('prints available commands through help command', function (): void {
    $app = getConfiguredApp();

    ob_start();
    $result = $app->runCommand(['minicli', 'help']);
    $output = (string) ob_get_clean();

    expect($result)->toBe(0)
        ->and($output)->toContain('Application Commands')
        ->and($output)->toContain('miniCLI Commands')
        ->and($output)->not->toContain('3rd-party Commands')
        ->and($output)->toContain('help')
        ->and($output)->toContain('test');
});
