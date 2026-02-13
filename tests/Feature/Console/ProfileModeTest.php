<?php

declare(strict_types=1);

it('shows profiling stats when profile flag is enabled', function (): void {
    $app = getConfiguredApp();

    ob_start();
    $result = $app->runCommand([
        'minicli',
        'test',
        'greet',
        'name=erika',
        '--shout',
        '--profile',
    ]);
    $output = (string) ob_get_clean();

    expect($result)->toBe(0)
        ->and($output)->toContain('HELLO ERIKA')
        ->and($output)->toContain('Command Profile')
        ->and($output)->toContain('test greet')
        ->and($output)->toContain('Exit code')
        ->and($output)->toContain('Time')
        ->and($output)->toContain('Memory delta')
        ->and($output)->toContain('Peak memory');
});

it('shows profiling stats even when command throws', function (): void {
    $app = getConfiguredApp();

    ob_start();

    try {
        $app->runCommand(['minicli', 'test', 'explode', '--profile']);
    } catch (RuntimeException) {
        // expected for this test
    }

    $output = (string) ob_get_clean();

    expect($output)->toContain('Command Profile')
        ->and($output)->toContain('test explode')
        ->and($output)->toContain('Exit code')
        ->and($output)->toContain('exception');
});
