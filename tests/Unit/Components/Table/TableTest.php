<?php

declare(strict_types=1);

use Minicli\Components\Component;
use Minicli\Components\Table\Row;
use Minicli\Components\Table\Table;
use Minicli\Components\Text;
use Minicli\Output\Filter\SimpleOutputFilter;

it('renders rows created with row objects', function (): void {
    $table = new Table([
        Row::make(['ID', 'NAME', 'FIELD3']),
        Row::make(['1', 'erika', 'value']),
    ]);

    $output = $table->output();

    expect($table->totalRows())->toBe(2)
        ->and($output)->toContain('ID')
        ->and($output)->toContain('erika');
});

it('is stable across repeated output calls', function (): void {
    $table = new Table();
    $table->addRow(Row::make(['ID', 'NAME']));
    $table->addRow(Row::make(['1', 'alpha']));

    $first = $table->output();
    $second = $table->output();

    expect($first)->toBe($second);
});

it('renders border mode consistently', function (): void {
    $table = Table::make([
        Row::make(['A', 'B']),
        Row::make(['left', 'right']),
    ])->withBorders();

    $output = $table->output();
    $lines = array_values(array_filter(explode("\n", trim($output))));
    $borderLines = array_values(array_filter($lines, static fn (string $line): bool => str_starts_with($line, '+')));

    expect($output)->toContain('+')
        ->toContain('|')
        ->and($borderLines)->toHaveCount(3);
});

it('keeps text alignment when rendering cells', function (): void {
    Component::setFilter(new SimpleOutputFilter());

    $table = Table::make([
        Row::make([
            Text::make('left')->width(8)->alignLeft(),
            Text::make('mid')->width(8)->alignCenter(),
            Text::make('right')->width(8)->alignRight(),
        ]),
    ])->withBorders();

    $output = $table->output();

    expect($output)->toContain('| left')
        ->toContain('  mid')
        ->toContain('right |');
});

it('applies row alignment to all cells', function (): void {
    Component::setFilter(new SimpleOutputFilter());

    $table = Table::make([
        Row::make(['A', 'B', 'C']),
        Row::make(['left', 'middle', 'right'])->alignCenter(),
    ])->withBorders();

    $output = $table->output();

    expect($output)->toContain('| left')
        ->toContain('middle')
        ->toContain('right |');
});

it('keeps bordered lines with matching widths for short values', function (): void {
    Component::setFilter(new SimpleOutputFilter());

    $table = Table::make([
        Row::make(['A', 'B']),
        Row::make(['1', '2']),
    ])->withBorders();

    $lines = array_values(array_filter(explode("\n", trim($table->output()))));
    $lineLengths = array_map(strlen(...), $lines);

    expect(array_unique($lineLengths))->toHaveCount(1);
});
