<?php

use Minicli\Input;

it('asserts that Input sets a default prompt', function () {
    $input = new Input();

    $this->assertEquals('minicli$> ', $input->getPrompt());
})->skip(function (): bool {
    return ! extension_loaded('readline');
}, 'Extension readline is required.');
