<?php

declare(strict_types=1);

namespace Minicli\Components\Table;

use Minicli\Components\Text;
use Minicli\Concerns\HasStyles;

final class Row
{
    use HasStyles;

    /** @var array<Text> */
    public array $cells;

    /**
     * @param  array<Text|string>  $cells
     */
    public function __construct(
        array $cells,
    ) {
        $this->cells = array_map(
            fn (Text|string $cell): Text => $cell instanceof Text ? $cell : Text::make($cell),
            $cells
        );
    }

    /**
     * @param  array<Text|string>  $cells
     */
    public static function make(array $cells): self
    {
        return new self($cells);
    }

    public function applyStylesToCells(): void
    {
        if (! $this->hasStyles()) {
            return;
        }

        foreach ($this->cells as $cell) {
            if (! $cell->hasStyles()) {
                $cell->applyStyles($this->styles());

                if ($this->isAlt()) {
                    $cell->alt();
                }
            }
        }
    }
}
