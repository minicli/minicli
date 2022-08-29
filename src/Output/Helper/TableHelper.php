<?php

declare(strict_types=1);

namespace Minicli\Output\Helper;

use Minicli\Output\Filter\SimpleOutputFilter;
use Minicli\Output\OutputFilterInterface;

class TableHelper
{
    /**
     * table rows
     *
     * @var array
     */
    protected array $tableRows;

    /**
     * style rows
     *
     * @var array
     */
    protected array $styledRows;

    /**
     * formatted table
     *
     * @var string
     */
    protected string $formattedTable = '';

    /**
     * TableHelper constructor. Optionally sets the table rows with an array containing all rows
     *
     * @param array|null $table
     */
    public function __construct(?array $table = null)
    {
        if (is_array($table)) {
            $this->setTable($table);
        }
    }

    /**
     * Returns the total number of rows in the table
     *
     * @return int
     */
    public function totalRows(): int
    {
        return count($this->tableRows);
    }

    /**
     * Adds a table header
     *
     * @param array $header
     * @param string $style
     * @return void
     */
    public function addHeader(array $header, $style = 'alt'): void
    {
        $this->insertTableRow($header, $style);
    }

    /**
     * Sets the table rows at once
     *
     * @param array $full_table An array containing each table row. Rows must be arrays containing the individual cell contents.
     * @return void
     */
    public function setTable(array $full_table): void
    {
        $first = true;

        foreach ($full_table as $row) {
            if ($first) {
                $this->addHeader($row);
                $first = false;
                continue;
            }

            $this->addRow($row);
        }
    }

    /**
     * Adds a table row
     *
     * @param array $row
     * @param string $style
     * @return void
     */
    public function addRow(array $row, string $style = 'default'): void
    {
        $this->insertTableRow($row, $style);
    }

    /**
     * Returns the formatted table for printing
     *
     * @param OutputFilterInterface|null $filter In case no filter is provided, a SimpleOutputFilter is used by default.
     * @return string
     */
    public function getFormattedTable(OutputFilterInterface $filter = null): string
    {
        $filter = $filter ?? new SimpleOutputFilter();

        foreach ($this->styledRows as $item) {
            $style = $item['style'];
            $row = $this->getRowAsString($item['row']);

            $this->formattedTable .= "\n" . $filter->filter($row, $style);
        }

        return $this->formattedTable;
    }

    /**
     * Inserts a new row in the table and sets the style for that row
     *
     * @param array $row
     * @param string $style
     * @return void
     */
    protected function insertTableRow(array $row, string $style = 'default'): void
    {
        $this->tableRows[] = $row;
        $this->styledRows[] = [ 'row' => $row, 'style' => $style ];
    }

    /**
     * Calculates ideal column sizes for the current table rows
     *
     * @param int $minColSize
     * @return array
     */
    protected function calculateColumnSizes(int $minColSize = 5): array
    {
        $columnSizes = [];

        foreach ($this->tableRows as $rowContent) {
            $columnCount = 0;

            foreach ($rowContent as $cell) {
                $columnSizes[$columnCount] = $columnSizes[$columnCount] ?? $minColSize;
                if (strlen($cell) >= $columnSizes[$columnCount]) {
                    $columnSizes[$columnCount] = strlen($cell) + 2;
                }
                $columnCount++;
            }
        }

        return $columnSizes;
    }

    /**
     * Transforms a row into a formatted string, with adequate column sizing
     *
     * @param array $row
     * @return string
     */
    protected function getRowAsString(array $row): string
    {
        //first, determine the size of each column
        $columnSizes  = $this->calculateColumnSizes();
        $formattedRow = "";

        foreach ($row as $column => $tableCell) {
            $formattedRow .= $this->getPaddedString($tableCell, $columnSizes[$column]);
        }

        return $formattedRow;
    }

    /**
     * Pads a string as table cell
     *
     * @param string $tableCell
     * @param int $colSize
     * @return string
     */
    protected function getPaddedString(string $tableCell, int $colSize = 5): string
    {
        return str_pad($tableCell, $colSize);
    }
}
