<?php

declare(strict_types=1);

namespace Minicli\Concerns;

use Minicli\Output\Theming\StyleType;

trait HasStyles
{
    /** @var array<StyleType> */
    private array $styles = [];

    private bool $altStyle = false;

    public function alt(): self
    {
        $this->altStyle = true;

        return $this;
    }

    public function normal(): self
    {
        $this->altStyle = false;

        return $this;
    }

    public function isAlt(): bool
    {
        return $this->altStyle;
    }

    public function default(): self
    {
        $this->addColorStyle(StyleType::DEFAULT);

        return $this;
    }

    public function error(): self
    {
        $this->addColorStyle(StyleType::ERROR);

        return $this;
    }

    public function warning(): self
    {
        $this->addColorStyle(StyleType::WARNING);

        return $this;
    }

    public function success(): self
    {
        $this->addColorStyle(StyleType::SUCCESS);

        return $this;
    }

    public function info(): self
    {
        $this->addColorStyle(StyleType::INFO);

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
        return $this->applyAltStyleTransformation($this->styles);
    }

    protected function addColorStyle(StyleType $style): void
    {
        $this->styles = array_filter(
            $this->styles,
            fn (StyleType $style): bool => ! in_array($style, StyleType::colorStyles())
        );

        $this->styles = [$style, ...$this->styles];
    }

    /**
     * @param  array<StyleType>  $styles
     * @return array<StyleType>
     */
    protected function applyAltStyleTransformation(array $styles): array
    {
        return array_map(
            fn (StyleType $style): StyleType => match ($style) {
                StyleType::DEFAULT, StyleType::ALT => $this->altStyle ? StyleType::ALT : StyleType::DEFAULT,
                StyleType::ERROR, StyleType::ERROR_ALT => $this->altStyle ? StyleType::ERROR_ALT : StyleType::ERROR,
                StyleType::WARNING, StyleType::WARNING_ALT => $this->altStyle ? StyleType::WARNING_ALT : StyleType::WARNING,
                StyleType::SUCCESS, StyleType::SUCCESS_ALT => $this->altStyle ? StyleType::SUCCESS_ALT : StyleType::SUCCESS,
                StyleType::INFO, StyleType::INFO_ALT => $this->altStyle ? StyleType::INFO_ALT : StyleType::INFO,
                default => $style,
            },
            $styles
        );
    }
}
