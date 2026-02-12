<?php

declare(strict_types=1);

use Minicli\Components\Component;
use Minicli\Components\Divider;
use Minicli\Output\Filter\ColorOutputFilter;
use Minicli\Output\Filter\SimpleOutputFilter;

beforeEach(function (): void {
    Component::setFilter(new SimpleOutputFilter());
});

it('renders divider with custom style and size', function (): void {
    expect(Divider::make('-', 3)->output())->toBe("---\n");
});

it('supports explicit width setter', function (): void {
    expect(Divider::make()->width(4)->output())->toBe("────\n");
});

it('supports full width divider from COLUMNS env', function (): void {
    $previousTerm = getenv('TERM');

    putenv('TERM=');
    putenv('COLUMNS=12');

    $output = Divider::make('-')->fullWidth()->output();

    if ($previousTerm !== false) {
        putenv("TERM={$previousTerm}");
    } else {
        putenv('TERM');
    }

    putenv('COLUMNS');

    expect($output)->toBe("------------\n");
});

it('supports styling through has styles trait', function (): void {
    $output = Divider::make('-')->warning()->bold()->output();

    expect($output)->toBe("-----\n");
});

it('applies dim style by default', function (): void {
    Component::setFilter(new ColorOutputFilter());

    $output = Divider::make('-')->output();

    expect($output)->toContain("\e[2m");
});
