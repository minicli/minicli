<?php

declare(strict_types=1);

namespace Minicli\Output\Table;

use Minicli\Output\Theming\StyleType;

final readonly class Row
{
    /** @var array<Cell> */
    public array $cells;

    /**
     * @param  array<Cell|string>  $cells
     */
    public function __construct(
        array $cells,
        public StyleType $style = StyleType::DEFAULT,
    ) {
        $this->cells = array_map(
            fn (Cell|string $cell): Cell => $cell instanceof Cell ? $cell : new Cell($cell, $style),
            $cells
        );
    }

    /**
     * @param  array<Cell|string>  $cells
     */
    public static function make(array $cells, StyleType $style = StyleType::DEFAULT): self
    {
        return new self($cells, $style);
    }
}
