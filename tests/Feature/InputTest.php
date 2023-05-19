<?php

declare(strict_types=1);

use Minicli\Input;

it('asserts that Input sets a default prompt', function (): void {
    expect((new Input())->getPrompt())
        ->toBe('minicli$> ');
})->skip(fn (): bool => ! extension_loaded('readline'), 'Extension readline is required.');
