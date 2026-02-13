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

    $plainOutput = stripAnsi($output);

    expect($result)->toBe(0)
        ->and($plainOutput)->toContain('HELLO ERIKA')
        ->and($plainOutput)->toContain('Command Profile')
        ->and($plainOutput)->toContain('test greet')
        ->and($plainOutput)->toContain('Exit code')
        ->and($plainOutput)->toContain('Time')
        ->and($plainOutput)->toContain(' ms')
        ->and($plainOutput)->toContain('Memory delta')
        ->and($plainOutput)->toContain('Peak memory');
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

    $plainOutput = stripAnsi($output);

    expect($plainOutput)->toContain('Command Profile')
        ->and($plainOutput)->toContain('test explode')
        ->and($plainOutput)->toContain('Exit code')
        ->and($plainOutput)->toContain('exception');
});

it('shows profile time in seconds when above one second', function (): void {
    $app = getConfiguredApp();

    ob_start();
    $result = $app->runCommand(['minicli', 'test', 'slow-profile', '--profile']);
    $output = (string) ob_get_clean();
    $plainOutput = stripAnsi($output);

    expect($result)->toBe(0)
        ->and($plainOutput)->toContain('test slow-profile')
        ->and($plainOutput)->toMatch('/Time\s*\|\s*\d+\.\d{2}\s+s/');
});

function stripAnsi(string $text): string
{
    return (string) preg_replace('/\e\[[\d;]*m/', '', $text);
}
