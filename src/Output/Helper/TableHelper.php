<?php


namespace Minicli\Output\Helper;

use Minicli\Output\Filter\SimpleOutputFilter;
use Minicli\Output\OutputFilterInterface;

class TableHelper
{
    /** @var array */
    protected $table_rows;

    /** @var array */
    protected $styled_rows;

    /** @var string */
    protected $formatted_table;

    /**
     * TableHelper constructor. Optionally sets the table rows with an array containing all rows.
     * @param array|null $table
     */
    public function __construct(array $table = null)
    {
        if (is_array($table)) {
            $this->setTable($table);
        }
    }

    /**
     * Returns the total number of rows in the table
     * @return int
     */
    public function totalRows() : int
    {
        return count($this->table_rows);
    }

    /**
     * Adds a table header
     * @param array $header
     * @param string $style
     */
    public function addHeader(array $header, $style = 'alt'): void
    {
        $this->insertTableRow($header, $style);
    }

    /**
     * Sets the table rows at once
     * @param array $full_table An array containing each table row. Rows must be arrays containing the individual cell contents.
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
     * @param array $row
     * @param string $style
     */
    public function addRow(array $row, $style = 'default'): void
    {
        $this->insertTableRow($row, $style);
    }

    /**
     * Returns the formatted table for printing
     * @param OutputFilterInterface $filter In case no filter is provided, a SimpleOutputFilter is used by default.
     * @return string
     */
    public function getFormattedTable(OutputFilterInterface $filter = null)
    {
        $filter = $filter ?? new SimpleOutputFilter();

        foreach ($this->styled_rows as $index => $item) {
            $style = $item['style'];
            $row = $this->getRowAsString($item['row']);

            $this->formatted_table .= "\n" . $filter->filter($row, $style);
        }

        return $this->formatted_table;
    }

    /**
     * Inserts a new row in the table and sets the style for that row
     * @param array $row
     * @param string $style
     */
    protected function insertTableRow(array $row, $style = 'default')
    {
        $this->table_rows[] = $row;
        $this->styled_rows[] = [ 'row' => $row, 'style' => $style ];
    }

    /**
     * Calculates ideal column sizes for the current table rows
     * @param int $min_col_size
     * @return array
     */
    protected function calculateColumnSizes($min_col_size = 5): array
    {
        $column_sizes = [];

        foreach ($this->table_rows as $row_number => $row_content) {
            $column_count = 0;

            foreach ($row_content as $cell) {
                $column_sizes[$column_count] = $column_sizes[$column_count] ?? $min_col_size;
                if (strlen($cell) >= $column_sizes[$column_count]) {
                    $column_sizes[$column_count] = strlen($cell) + 2;
                }
                $column_count++;
            }
        }
        
        return $column_sizes;
    }

    /**
     * Transforms a row into a formatted string, with adequate column sizing
     * @param array $row
     * @param int $col_size
     * @return string
     */
    protected function getRowAsString(array $row, $col_size = 5): string
    {
        //first, determine the size of each column
        $column_sizes = $this->calculateColumnSizes();

        $formatted_row = "";

        foreach ($row as $column => $table_cell) {
            $formatted_row .= $this->getPaddedString($table_cell, $column_sizes[$column]);
        }

        return $formatted_row;
    }

    /**
     * Pads a string as table cell
     * @param string $table_cell
     * @param int $col_size
     * @return string
     */
    protected function getPaddedString($table_cell, $col_size = 5): string
    {
        return str_pad($table_cell, $col_size);
    }
}
