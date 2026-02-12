<?php

declare(strict_types=1);

it('prints available commands through help command', function (): void {
    $app = getConfiguredApp();

    ob_start();
    $result = $app->runCommand(['minicli', 'help']);
    $output = (string) ob_get_clean();

    expect($result)->toBe(0)
        ->and($output)->toContain('Available commands:')
        ->and($output)->toContain('help')
        ->and($output)->toContain('test');
});
