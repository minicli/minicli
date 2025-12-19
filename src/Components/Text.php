<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Output\Theming\StyleType;

class Text extends Component
{
    /** @var array<StyleType> */
    private array $styles = [];

    private bool $lineBreak = true;

    public function __construct(
        private string $content = '',
    ) {}

    public static function make(string $content): self
    {
        return new self($content);
    }

    public function default(bool $alt = false): self
    {
        $this->addColorStyle($alt ? StyleType::ALT : StyleType::DEFAULT);

        return $this;
    }

    public function error(bool $alt = false): self
    {
        $this->addColorStyle($alt ? StyleType::ERROR_ALT : StyleType::ERROR);

        return $this;
    }

    public function warning(bool $alt = false): self
    {
        $this->addColorStyle($alt ? StyleType::WARNING_ALT : StyleType::WARNING);

        return $this;
    }

    public function success(bool $alt = false): self
    {
        $this->addColorStyle($alt ? StyleType::SUCCESS_ALT : StyleType::SUCCESS);

        return $this;
    }

    public function info(bool $alt = false): self
    {
        $this->addColorStyle($alt ? StyleType::INFO_ALT : StyleType::INFO);

        return $this;
    }

    public function bold(): self
    {
        $this->styles[] = StyleType::BOLD;

        return $this;
    }

    public function dim(): self
    {
        $this->styles[] = StyleType::DIM;

        return $this;
    }

    public function italic(): self
    {
        $this->styles[] = StyleType::ITALIC;

        return $this;
    }

    public function underline(): self
    {
        $this->styles[] = StyleType::UNDERLINE;

        return $this;
    }

    public function invert(): self
    {
        $this->styles[] = StyleType::INVERT;

        return $this;
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
        $output = "{$this->printer()->out($this->filter()->filter($this->content, $this->styles))}";

        return $this->lineBreak ? "{$output}\n" : $output;
    }

    protected function addColorStyle(StyleType $style): void
    {
        $this->styles = array_filter(
            $this->styles,
            fn (StyleType $style): bool => ! in_array($style, StyleType::colorStyles())
        );

        $this->styles = [$style, ...$this->styles];
    }
}
