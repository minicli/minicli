<?php

namespace Minicli\Output;

final class ThemeStyle
{
    public function __construct(
        public readonly string $foreground,
        public readonly ?string $background = null
    ) {
    }

    public static function make(string $foreground, ?string $background = null): self
    {
        return new self($foreground, $background);
    }
}
