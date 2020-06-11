<?php

use Minicli\Input;

it('asserts that Input sets a default prompt', function () {
    $input = new Input();

    assertEquals('minicli$> ', $input->getPrompt());
});
