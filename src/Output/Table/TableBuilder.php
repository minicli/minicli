<?php

declare(strict_types=1);

namespace Minicli\Output\Table;

use Minicli\Contracts\OutputFilterInterface;
use Minicli\Output\Filter\SimpleOutputFilter;

class TableBuilder
{
    /** @var array<Row> */
    protected array $rows;

    /**
     * @param  array<Row>|null  $table
     */
    public function __construct(?array $table = null)
    {
        if (is_array($table)) {
            $this->setTable($table);
        }
    }

    /**
     * @param  array<Row>|null  $table
     */
    public static function make(?array $table = null): self
    {
        return new self($table);
    }

    public function addRow(Row $row): void
    {
        $this->rows[] = $row;
    }

    public function totalRows(): int
    {
        return count($this->rows);
    }

    /**
     * @param  OutputFilterInterface|null  $filter  In case no filter is provided, a SimpleOutputFilter is used by default.
     */
    public function table(?OutputFilterInterface $filter = null): string
    {
        $filter ??= new SimpleOutputFilter();
        $table = '';

        foreach ($this->rows as $row) {
            $style = $row->style;
            $row = $this->rowToString($row->cells);

            $table .= "\n{$filter->filter($row, $style)}";
        }

        return $table;
    }

    /**
     * @param  array<Row>  $fullTable
     */
    protected function setTable(array $fullTable): void
    {
        foreach ($fullTable as $row) {
            $this->addRow($row);
        }
    }

    /**
     * @return array<int, int>
     */
    protected function calculateColumnSizes(int $minColSize = 5): array
    {
        $columnSizes = [];

        foreach ($this->rows as $rowContent) {
            $columnCount = 0;

            foreach ($rowContent->cells as $cell) {
                $columnSizes[$columnCount] ??= $minColSize;
                if (mb_strlen($cell) >= $columnSizes[$columnCount]) {
                    $columnSizes[$columnCount] = mb_strlen($cell) + 2;
                }
                $columnCount++;
            }
        }

        return $columnSizes;
    }

    /**
     * @param  array<int, string>  $row
     */
    protected function rowToString(array $row): string
    {
        // first, determine the size of each column
        $columnSizes = $this->calculateColumnSizes();
        $formattedRow = '';

        foreach ($row as $column => $tableCell) {
            $formattedRow .= $this->getPaddedString($tableCell, $columnSizes[$column]);
        }

        return $formattedRow;
    }

    protected function getPaddedString(string $tableCell, int $colSize = 5): string
    {
        return mb_str_pad($tableCell, $colSize);
    }
}
