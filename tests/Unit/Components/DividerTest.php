<?php

declare(strict_types=1);

use Minicli\Components\Divider;

it('renders divider with custom style and size', function (): void {
    expect(Divider::make('-', 3)->output())->toBe("---\n");
});
