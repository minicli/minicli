<?php

declare(strict_types=1);

use Minicli\Output\CLI\Foreground;
use Minicli\Output\Filter\ColorOutputFilter;
use Minicli\Output\Filter\SimpleOutputFilter;
use Minicli\Output\Filter\TimestampOutputFilter;
use Minicli\Output\Theme\UnicornTheme;

it('asserts that SimpleOutputFilter returns unstyled content')
    ->expect(new SimpleOutputFilter()->filter('My content'))
    ->toBe('My content');

it('asserts that ColorOutputFilter returns styled content with default theme')
    ->expect(new ColorOutputFilter()->filter('My content'))
    ->toBe(sprintf("\e[%sm%s\e[0m", Foreground::WHITE->value, 'My content'));

it('asserts that ColorOutputFilter sets theme correctly and formats with style', function (): void {
    $color = new ColorOutputFilter();
    $color->setTheme(new UnicornTheme());

    $text = 'My content';
    $styled = $color->filter($text, 'info');
    $expected = sprintf("\e[%sm%s\e[0m", Foreground::MAGENTA->value, $text);

    expect($color->theme())->toBeInstanceOf(UnicornTheme::class)
        ->and($styled)->toBe($expected);
});

it('asserts that TimestampOutputFilter adds timestamp to messages')
    ->expect(new TimestampOutputFilter()->filter('test timestamp'))
    ->toContain(new DateTimeImmutable()->format('Y-m-d'))
    ->toContain('test timestamp');

it('asserts that TimestampOutputFilter adds formatted timestamp to messages')
    ->expect(new TimestampOutputFilter()->filter('test timestamp', 'm/d/Y'))
    ->toContain(new DateTimeImmutable()->format('m/d/Y'))
    ->toContain('test timestamp');
