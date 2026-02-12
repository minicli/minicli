<?php

declare(strict_types=1);

use Minicli\Components\Component;
use Minicli\Components\Text;
use Minicli\Output\Filter\SimpleOutputFilter;

it('renders text with and without trailing line break', function (): void {
    $text = Text::make('hello');

    $withBreak = $text->output();
    $withoutBreak = $text->withoutLineBreak()->output();

    expect($withBreak)->toContain('hello')
        ->and($withBreak)->toEndWith("\n")
        ->and($withoutBreak)->toContain('hello')
        ->and($withoutBreak)->not->toEndWith("\n");
});

it('supports left, center and right alignment with width', function (): void {
    Component::setFilter(new SimpleOutputFilter());

    $left = Text::make('hi')->width(6)->alignLeft()->withoutLineBreak()->output();
    $center = Text::make('hi')->width(6)->alignCenter()->withoutLineBreak()->output();
    $right = Text::make('hi')->width(6)->alignRight()->withoutLineBreak()->output();

    expect($left)->toBe('hi    ')
        ->and($center)->toBe('  hi  ')
        ->and($right)->toBe('    hi');
});
