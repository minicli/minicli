<?php

declare(strict_types=1);

use Minicli\Components\Component;
use Minicli\Components\ProgressBar;
use Minicli\Output\Filter\SimpleOutputFilter;

it('formats progress bar output', function (): void {
    Component::setFilter(new SimpleOutputFilter());

    $bar = ProgressBar::make('Working');
    $bar->setProgress(50);
    $output = $bar->output();

    expect($output)->toContain('Working')
        ->toContain('50%')
        ->toContain('[')
        ->toContain(']');
});

it('runs through all configured steps', function (): void {
    Component::setFilter(new SimpleOutputFilter());

    $bar = ProgressBar::make('Run');
    $processed = [];
    $bar
        ->steps(['a', 'b'])
        ->callback(function (mixed $step) use (&$processed): void {
            $processed[] = $step;
        })
        ->run();

    expect($processed)->toBe(['a', 'b'])
        ->and($bar->output())->toContain('Run')
        ->and($bar->output())->toContain('2/2')
        ->toContain('100%');
});
