<?php

declare(strict_types=1);

namespace Minicli\Output;

final class ThemeConfig
{
    /** @var array<string, ThemeStyle> */
    private array $customStyles = [];

    public function __construct(
        public ThemeStyle $default,
        public ThemeStyle $alt,
        public ThemeStyle $error,
        public ThemeStyle $error_alt,
        public ThemeStyle $success,
        public ThemeStyle $success_alt,
        public ThemeStyle $info,
        public ThemeStyle $info_alt,
        public ThemeStyle $bold,
        public ThemeStyle $dim,
        public ThemeStyle $italic,
        public ThemeStyle $underline,
        public ThemeStyle $invert
    ) {
    }

    public function __get(string $name): ?ThemeStyle
    {
        return $this->customStyles[$name] ?? null;
    }

    public function __set(string $name, ThemeStyle $value): void
    {
        $this->customStyles[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->customStyles[$name]);
    }

    public static function make(
        ThemeStyle $default,
        ThemeStyle $alt,
        ThemeStyle $error,
        ThemeStyle $error_alt,
        ThemeStyle $success,
        ThemeStyle $success_alt,
        ThemeStyle $info,
        ThemeStyle $info_alt,
        ThemeStyle $bold,
        ThemeStyle $dim,
        ThemeStyle $italic,
        ThemeStyle $underline,
        ThemeStyle $invert
    ): self {
        return new self(
            $default,
            $alt,
            $error,
            $error_alt,
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
