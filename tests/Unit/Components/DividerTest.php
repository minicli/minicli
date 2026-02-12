<?php

declare(strict_types=1);

use Minicli\Components\Divider;

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
