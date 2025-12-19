<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Concerns\HasStyles;

class Text extends Component
{
    use HasStyles;

    private bool $lineBreak = true;

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

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function output(): string
    {
        $output = "{$this->printer()->out($this->filter()->filter($this->content, $this->styles()))}";

        return $this->lineBreak ? "{$output}\n" : $output;
    }
}
