<?php

declare(strict_types=1);

use Minicli\Input\Input;

it('uses modern default prompt', function (): void {
    expect(new Input()->getPrompt())->toBe('> ');
});

it('allows overriding prompt value', function (): void {
    $input = new Input(prompt: 'minicli> ');

    expect($input->getPrompt())->toBe('minicli> ');
});
