<?php

declare(strict_types=1);

namespace Minicli\Components\Table;

use Minicli\Components\Component;
use Minicli\Components\Text;

class Table extends Component
{
    /** @var array<Row> */
    protected array $rows = [];

    protected bool $withBorders = false;

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

    public function withBorders(): self
    {
        $this->withBorders = true;

        return $this;
    }

    public function output(): string
    {
        $columnSizes = $this->calculateColumnSizes();
        $output = '';

        if ($this->withBorders) {
            $output .= $this->borderLine($columnSizes);
        }

        foreach ($this->rows as $rowIndex => $row) {
            if ($this->withBorders) {
                $output .= '|';
            }

            $row->applyStylesToCells();
            foreach ($row->cells as $columnIndex => $cell) {
                $columnSize = $columnSizes[$columnIndex];
                $innerSize = $this->withBorders ? max(0, $columnSize - 2) : $columnSize;
                $paddedContent = paddedString($cell->content(), $innerSize);

                if ($this->withBorders) {
                    $paddedContent = " {$paddedContent} ";
                }

                $textCell = Text::make($paddedContent)->applyStyles($cell->styles());
                if ($cell->isAlt()) {
                    $textCell->alt();
                }

                $output .= $textCell->withoutLineBreak()->output();

                if ($this->withBorders) {
                    $output .= '|';
                }
            }

            $output .= "\n";

            if ($this->withBorders && $rowIndex === 0 && $this->totalRows() > 1) {
                $output .= $this->borderLine($columnSizes);
            }
        }

        if ($this->withBorders && $this->rows !== []) {
            $output .= $this->borderLine($columnSizes);
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

    /**
     * @param  array<int>  $columnSizes
     */
    protected function borderLine(array $columnSizes): string
    {
        $line = '+';

        foreach ($columnSizes as $columnSize) {
            $line .= str_repeat('-', $columnSize) . '+';
        }

        return $line . "\n";
    }
}
