<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Concerns\HasStyles;

class Divider extends Component
{
    use HasStyles;

    private bool $useFullWidth = false;

    public function __construct(
        private string $style = '─',
        private int $width = 5,
    ) {
        $this->dim();
    }

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
        $line = str_repeat($this->style, $this->resolvedWidth());

        return $this->printer()->out($this->filter()->filter($line, $this->styles())) . "\n";
    }

    private function resolvedWidth(): int
    {
        if (! $this->useFullWidth) {
            return $this->width;
        }

        return max(1, $this->terminalWidth());
    }
}
