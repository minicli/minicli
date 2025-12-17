<?php

declare(strict_types=1);

namespace Minicli\Output\Theming;

final class ThemeConfig
{
    /**
     * @var array<string, ThemeStyle>
     */
    private array $customStyles = [];

    public function __construct(
        public ?ThemeStyle $default = null,
        public ?ThemeStyle $alt = null,
        public ?ThemeStyle $error = null,
        public ?ThemeStyle $error_alt = null,
        public ?ThemeStyle $warning = null,
        public ?ThemeStyle $warning_alt = null,
        public ?ThemeStyle $success = null,
        public ?ThemeStyle $success_alt = null,
        public ?ThemeStyle $info = null,
        public ?ThemeStyle $info_alt = null,
        public ?ThemeStyle $bold = null,
        public ?ThemeStyle $dim = null,
        public ?ThemeStyle $italic = null,
        public ?ThemeStyle $underline = null,
        public ?ThemeStyle $invert = null,
    ) {}

    public function __get(string $name): ?ThemeStyle
    {
        return $this->customStyles[$name] ?? null;
    }

    public function __set(string $name, ThemeStyle $value): void
    {
        $this->customStyles[$name] = $value;
    }

    public static function make(
        ?ThemeStyle $default = null,
        ?ThemeStyle $alt = null,
        ?ThemeStyle $error = null,
        ?ThemeStyle $error_alt = null,
        ?ThemeStyle $warning = null,
        ?ThemeStyle $warning_alt = null,
        ?ThemeStyle $success = null,
        ?ThemeStyle $success_alt = null,
        ?ThemeStyle $info = null,
        ?ThemeStyle $info_alt = null,
        ?ThemeStyle $bold = null,
        ?ThemeStyle $dim = null,
        ?ThemeStyle $italic = null,
        ?ThemeStyle $underline = null,
        ?ThemeStyle $invert = null,
    ): self {
        return new self(
            $default,
            $alt,
            $error,
            $error_alt,
            $warning,
            $warning_alt,
            $success,
            $success_alt,
            $info,
            $info_alt,
            $bold,
            $dim,
            $italic,
            $underline,
            $invert
        );
    }
}
