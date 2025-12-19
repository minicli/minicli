<?php

declare(strict_types=1);

namespace Minicli\Components;

class Divider extends Component
{
    public function __construct(
        private string $style = '-',
        private int $size = 5,
    ) {}

    public static function make(string $style = '-', int $size = 5): self
    {
        return new self($style, $size);
    }

    public function style(string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function output(): string
    {
        return str_repeat($this->style, $this->size) . "\n";
    }
}
