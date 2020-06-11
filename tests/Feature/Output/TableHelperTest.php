<?php
use Minicli\Output\Helper\TableHelper;
use Minicli\Output\Filter\ColorOutputFilter;

it('asserts that TableHelper creates table from constructor', function () {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value1', 'value2', 'value3']
    ];


    $table_helper = new TableHelper($table);

    assertEquals(2, $table_helper->totalRows());
    $table_content = $table_helper->getFormattedTable();

    assertStringContainsString('value1', $table_content);
    assertStringContainsString('value2', $table_content);
    assertStringContainsString('value3', $table_content);
});

it('asserts that TableHelper sets and outputs table rows', function () {
    $table = new TableHelper();

    $table->addHeader(
        ['ID', 'NAME', 'FIELD3']
    );

    for ($i = 1; $i <= 10; $i++) {
        $table->addRow([
            $i, 'test', rand(0, 200)
        ]);
    }

    assertEquals(11, $table->totalRows());
    $table_content = $table->getFormattedTable();

    assertStringContainsString('ID', $table_content);
    assertStringContainsString('NAME', $table_content);
    assertStringContainsString('FIELD3', $table_content);
});

it('asserts that all fields respect column sizes', function () {
    $table = [
        ['ID', 'NAME', 'FIELD3'],
        ['value11234123', 'value2234', 'value3as2341234123'],
        ['value1', 'value2', 'value3']
    ];


    $table_helper = new TableHelper($table);
    $table_content = $table_helper->getFormattedTable();

    $rows = explode("\n", $table_content);
    $size_at_first = strlen($rows[1]);
    $size_at_last = strlen($rows[count($rows)-1]);

    assertSame($size_at_first, $size_at_last);
});
