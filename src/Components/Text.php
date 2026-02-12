<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Concerns\HasStyles;
use Minicli\Support\Alignment;

class Text extends Component
{
    use HasStyles;

    private bool $lineBreak = true;

    private Alignment $alignment = Alignment::Left;

    private ?int $alignmentWidth = null;

    public function __construct(
        private string $content,
    ) {}

    public static function make(string $content): self
    {
        return new self($content);
    }

    public function withLineBreak(): self
    {
        $this->lineBreak = true;

        return $this;
    }

    public function withoutLineBreak(): self
    {
        $this->lineBreak = false;

        return $this;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function width(int $width): self
    {
        $this->alignmentWidth = max(1, $width);

        return $this;
    }

    public function fullWidth(): self
    {
        $this->alignmentWidth = max(1, $this->terminalWidth());

        return $this;
    }

    public function alignLeft(): self
    {
        $this->alignment = Alignment::Left;

        return $this;
    }

    public function alignCenter(): self
    {
        $this->alignment = Alignment::Center;

        return $this;
    }

    public function alignRight(): self
    {
        $this->alignment = Alignment::Right;

        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function output(): string
    {
        $output = "{$this->printer()->out($this->filter()->filter($this->alignedContent(), $this->styles()))}";

        return $this->lineBreak ? "{$output}\n" : $output;
    }

    private function alignedContent(): string
    {
        if ($this->alignmentWidth === null) {
            return $this->content;
        }

        $contentWidth = $this->stringWidth($this->content);
        $width = max($this->alignmentWidth, $contentWidth);

        return match ($this->alignment) {
            Alignment::Right => mb_str_pad($this->content, $width, ' ', STR_PAD_LEFT),
            Alignment::Center => mb_str_pad($this->content, $width, ' ', STR_PAD_BOTH),
            default => mb_str_pad($this->content, $width, ' ', STR_PAD_RIGHT),
        };
    }
}
