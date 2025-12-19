<?php

declare(strict_types=1);

namespace Minicli\Concerns;

use Minicli\Output\Theming\StyleType;

trait HasStyles
{
    /** @var array<StyleType> */
    private array $styles = [];

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

    public function hasStyles(): bool
    {
        return $this->styles !== [];
    }

    /**
     * @param  array<StyleType>  $styles
     */
    public function applyStyles(array $styles): self
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * @return array<StyleType>
     */
    public function styles(): array
    {
        return $this->styles;
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
