<?php

use Minicli\Input;

it('asserts that Input sets a default prompt', function () {
    expect((new Input())->getPrompt())
        ->toBe('minicli$> ');
})->skip(function (): bool {
    return ! extension_loaded('readline');
}, 'Extension readline is required.');
