<?php

use Minicli\Output\Helper\TableHelper;

it('asserts that TableHelper creates table from constructor', function () {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value1', 'value2', 'value3']
    ];


    $tableHelper = new TableHelper($table);

    $this->assertEquals(2, $tableHelper->totalRows());
    $tableContent = $tableHelper->getFormattedTable();

    $this->assertStringContainsString('value1', $tableContent);
    $this->assertStringContainsString('value2', $tableContent);
    $this->assertStringContainsString('value3', $tableContent);
});

it('asserts that TableHelper sets and outputs table rows', function () {
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

    $this->assertEquals(11, $table->totalRows());
    $tableContent = $table->getFormattedTable();

    $this->assertStringContainsString('ID', $tableContent);
    $this->assertStringContainsString('NAME', $tableContent);
    $this->assertStringContainsString('FIELD3', $tableContent);
});

it('asserts that all fields respect column sizes', function () {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value11234123', 'value2234', 'value3as2341234123'],
        ['value1', 'value2', 'value3']
    ];


    $tableHelper = new TableHelper($table);
    $tableContent = $tableHelper->getFormattedTable();

    $rows = explode("\n", $tableContent);
    $sizeAtFirst = strlen($rows[1]);
    $sizeAtLast = strlen($rows[count($rows)-1]);

    $this->assertSame($sizeAtFirst, $sizeAtLast);
});
