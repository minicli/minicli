<?php

declare(strict_types=1);

use Minicli\Components\Alert;
use Minicli\Components\Component;
use Minicli\Output\Filter\SimpleOutputFilter;

it('renders alert content box', function (): void {
    Component::setFilter(new SimpleOutputFilter());

    $output = Alert::make('Something happened', 'NOTICE')->info()->output();

    expect($output)->toContain('NOTICE')
        ->and($output)->toContain('Something happened');
});
