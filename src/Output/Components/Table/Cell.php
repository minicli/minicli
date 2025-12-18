<?php

declare(strict_types=1);

namespace Minicli\Output\Components\Table;

use Minicli\Output\Theming\StyleType;

final readonly class Cell
{
    public function __construct(
        public string $content,
        public StyleType $style = StyleType::DEFAULT,
    ) {}

    public static function make(string $content, StyleType $style = StyleType::DEFAULT): self
    {
        return new self($content, $style);
    }
}
