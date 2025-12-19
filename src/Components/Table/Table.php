<?php

declare(strict_types=1);

namespace Minicli\Components\Table;

use Minicli\Components\Component;

class Table extends Component
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

    public function output(): string
    {
        $columnSizes = $this->calculateColumnSizes();
        $output = '';

        foreach ($this->rows as $row) {
            $row->applyStylesToCells();
            foreach ($row->cells as $columnIndex => $cell) {
                $paddedContent = paddedString(
                    $cell->content(),
                    $columnSizes[$columnIndex]
                );

                $cell->setContent($paddedContent);
                $output .= $cell->withoutLineBreak()->output();
            }

            $output .= "\n";
        }

        return $output;
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
                if ($this->stringWidth($cell->content()) >= $columnSizes[$columnCount]) {
                    $columnSizes[$columnCount] = $this->stringWidth($cell->content()) + 2;
                }
                $columnCount++;
            }
        }

        return $columnSizes;
    }
}
