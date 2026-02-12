<?php

declare(strict_types=1);

use Minicli\Components\Text;

it('renders text with and without trailing line break', function (): void {
    $text = Text::make('hello');

    $withBreak = $text->output();
    $withoutBreak = $text->withoutLineBreak()->output();

    expect($withBreak)->toContain('hello')
        ->and($withBreak)->toEndWith("\n")
        ->and($withoutBreak)->toContain('hello')
        ->and($withoutBreak)->not->toEndWith("\n");
});
