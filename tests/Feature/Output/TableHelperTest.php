<?php

declare(strict_types=1);

use Minicli\Components\Table\Table;

it('asserts that Table creates table from constructor', function (): void {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value1', 'value2', 'value3'],
    ];

    $tableHelper = new Table($table);
    $tableContent = $tableHelper->table();

    expect($tableHelper->totalRows())->toBe(2)
        ->and($tableContent)->toContain('value1')
        ->and($tableContent)->toContain('value2')
        ->and($tableContent)->toContain('value3');
});

it('asserts that Table sets and outputs table rows', function (): void {
    $table = new Table();

    $table->addHeader(
        ['ID', 'NAME', 'FIELD3']
    );

    for ($i = 1; $i <= 10; $i++) {
        $table->addRow([
            (string) $i,
            'test',
            (string) random_int(0, 200),
        ]);
    }

    $tableContent = $table->table();

    expect($table->totalRows())->toBe(11)
        ->and($tableContent)->toContain('ID')
        ->and($tableContent)->toContain('NAME')
        ->and($tableContent)->toContain('FIELD3');
});

it('asserts that all fields respect column sizes', function (): void {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value11234123', 'value2234', 'value3as2341234123'],
        ['value1', 'value2', 'value3'],
    ];

    $tableHelper = new Table($table);
    $tableContent = $tableHelper->table();

    $rows = explode("\n", $tableContent);
    $sizeAtFirst = mb_strlen($rows[1]);
    $sizeAtLast = mb_strlen($rows[count($rows) - 1]);

    expect($sizeAtFirst)->toBe($sizeAtLast);
});
