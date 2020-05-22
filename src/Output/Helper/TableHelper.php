<?php


namespace Minicli\Output\Helper;


class TableHelper
{
    /** @var array */
    protected $table;

    public function __construct(array $table = [])
    {
        $this->table = $table;
    }

    /**
     * @param array $table
     * @param int $min_col_size
     * @param bool $with_header
     */
    public function printTable(array $table, $min_col_size = 10, $with_header = true, $spacing = true)
    {
        $first = true;

        if ($spacing) {
            $this->newline();
        }

        foreach ($table as $index => $row) {

            if ($first && $with_header) {
                array_map(function ($item) {
                    return strtoupper($item);
                }, $row);
            }

            $this->out($helper->getRow($table, $index, $min_col_size));
            $first = false;
        }

        if ($spacing) {
            $this->newline();
        }
    }

    /**
     * @param array $table
     * @param int $row
     * @param int $min_col_size
     * @return string
     */
    public function getRow(array $table, $row, $min_col_size = 5)
    {
        $cells = "";

        foreach ($table[$row] as $column => $table_cell) {
            $col_size = self::calculateColumnSize($column, $table, $min_col_size);

            $cells .= $this->getPaddedString($table_cell, $col_size) . "\n";
        }

        return $cells;
    }

    /**
     * @param string $table_cell
     * @param int $col_size
     * @return string
     */
    public function getPaddedString($table_cell, $col_size = 5)
    {
        return str_pad($table_cell, $col_size);
    }

    /**
     * @param $column
     * @param array $table
     * @param int $min_col_size
     * @return int
     */
    public static function calculateColumnSize($column, array $table, $min_col_size = 5)
    {
        $size = $min_col_size;

        foreach ($table as $row) {
            $size = strlen($row[$column]) >= $size ? strlen($row[$column]) + 2 : $size;
        }

        return $size;
    }
}