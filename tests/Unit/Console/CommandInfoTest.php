<?php

declare(strict_types=1);

use Minicli\Console\ArgumentInfo;
use Minicli\Console\CommandInfo;
use Minicli\Console\ExitCode;

it('displays no-arguments message when command has no parameters', function (): void {
    $info = new CommandInfo(
        callable: static fn (): ExitCode => ExitCode::Success,
        name: 'demo',
        description: 'Demo command',
    );

    ob_start();
    $result = $info->displayHelp();
    $output = (string) ob_get_clean();

    expect($result)->toBe(ExitCode::Success)
        ->and($output)->toContain('This command has no arguments.')
        ->and($output)->toContain('Global Flags')
        ->and($output)->toContain('--help');
});

it('prints argument table when command has parameters', function (): void {
    $info = new CommandInfo(
        callable: static fn (): ExitCode => ExitCode::Success,
        name: 'demo',
        description: 'Demo command',
        arguments: [
            new ArgumentInfo('count', 'Total count', true),
        ],
    );

    ob_start();
    $result = $info->displayHelp();
    $output = (string) ob_get_clean();

    expect($result)->toBe(ExitCode::Success)
        ->and($output)->toContain('ARGUMENT')
        ->and($output)->toContain('count')
        ->and($output)->toContain('+')
        ->and($output)->toContain('Global Flags');
});
