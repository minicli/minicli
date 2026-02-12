<?php

declare(strict_types=1);

use Minicli\Components\LineBreak;

it('renders expected line break amount', function (): void {
    expect(LineBreak::make(2)->output())->toBe("\n\n");
});
