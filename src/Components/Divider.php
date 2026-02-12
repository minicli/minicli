<?php

declare(strict_types=1);

namespace Minicli\Components;

class Divider extends Component
{
    private bool $useFullWidth = false;

    public function __construct(
        private string $style = '─',
        private int $width = 5,
    ) {}

    public static function make(string $style = '─', int $width = 5): self
    {
        return new self($style, $width);
    }

    public function style(string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function size(int $size): self
    {
        return $this->width($size);
    }

    public function width(int $width): self
    {
        $this->width = max(1, $width);
        $this->useFullWidth = false;

        return $this;
    }

    public function fullWidth(): self
    {
        $this->useFullWidth = true;

        return $this;
    }

    public function output(): string
    {
        return str_repeat($this->style, $this->resolvedWidth()) . "\n";
    }

    private function resolvedWidth(): int
    {
        if (! $this->useFullWidth) {
            return $this->width;
        }

        return max(1, $this->terminalWidth());
    }
}
