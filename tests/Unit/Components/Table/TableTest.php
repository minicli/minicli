<?php

declare(strict_types=1);

use Minicli\Components\Table\Row;
use Minicli\Components\Table\Table;

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
