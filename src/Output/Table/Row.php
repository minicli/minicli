<?php

declare(strict_types=1);

namespace Minicli\Output\Table;

use Minicli\Output\Theming\StyleType;

final readonly class Row
{
    public function __construct(
        /** @var array<string> */
        public array $cells,
        public StyleType $style = StyleType::DEFAULT,
    ) {}

    /**
     * @param  array<string>  $cells
     */
    public static function make(array $cells, StyleType $style = StyleType::DEFAULT): self
    {
        return new self($cells, $style);
    }
}
