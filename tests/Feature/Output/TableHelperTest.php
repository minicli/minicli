<?php

declare(strict_types=1);

use Minicli\Output\Helper\TableHelper;

it('asserts that TableHelper creates table from constructor', function (): void {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value1', 'value2', 'value3']
    ];

    $tableHelper = new TableHelper($table);
    $tableContent = $tableHelper->getFormattedTable();

    expect($tableHelper->totalRows())->toBe(2)
        ->and($tableContent)->toContain('value1')
        ->and($tableContent)->toContain('value2')
        ->and($tableContent)->toContain('value3');
});

it('asserts that TableHelper sets and outputs table rows', function (): void {
    $table = new TableHelper();

    $table->addHeader(
        ['ID', 'NAME', 'FIELD3']
    );

    for ($i = 1; $i <= 10; $i++) {
        $table->addRow([
            (string) $i,
            'test',
            (string) rand(0, 200)
        ]);
    }

    $tableContent = $table->getFormattedTable();

    expect($table->totalRows())->toBe(11)
        ->and($tableContent)->toContain('ID')
        ->and($tableContent)->toContain('NAME')
        ->and($tableContent)->toContain('FIELD3');
});

it('asserts that all fields respect column sizes', function (): void {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value11234123', 'value2234', 'value3as2341234123'],
        ['value1', 'value2', 'value3']
    ];


    $tableHelper = new TableHelper($table);
    $tableContent = $tableHelper->getFormattedTable();

    $rows = explode("\n", $tableContent);
    $sizeAtFirst = mb_strlen($rows[1]);
    $sizeAtLast = mb_strlen($rows[count($rows)-1]);

    expect($sizeAtFirst)->toBe($sizeAtLast);
});
