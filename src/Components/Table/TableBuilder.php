<?php

declare(strict_types=1);

namespace Minicli\Components\Table;

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
            $table .= "\n{$this->rowToString($row->cells, $filter)}";
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
     * @return array<int>
     */
    protected function calculateColumnSizes(int $minColSize = 5): array
    {
        $columnSizes = [];

        foreach ($this->rows as $rowContent) {
            $columnCount = 0;

            foreach ($rowContent->cells as $cell) {
                $columnSizes[$columnCount] ??= $minColSize;
                if (mb_strlen($cell->content) >= $columnSizes[$columnCount]) {
                    $columnSizes[$columnCount] = mb_strlen($cell->content) + 2;
                }
                $columnCount++;
            }
        }

        return $columnSizes;
    }

    /**
     * @param  array<Cell>  $row
     */
    protected function rowToString(array $row, OutputFilterInterface $filter): string
    {
        // first, determine the size of each column
        $columnSizes = $this->calculateColumnSizes();
        $formattedRow = '';

        foreach ($row as $column => $cell) {
            $paddedContent = $this->getPaddedString($cell->content, $columnSizes[$column]);
            $formattedRow .= $filter->filter($paddedContent, [$cell->style]);
        }

        return $formattedRow;
    }

    protected function getPaddedString(string $tableCell, int $colSize = 5): string
    {
        return mb_str_pad($tableCell, $colSize);
    }
}
