<?php

declare(strict_types=1);

namespace Minicli\Output;

final readonly class ThemeStyle
{
    public function __construct(
        public string $foreground,
        public ?string $background = null
    ) {}

    public static function make(string $foreground, ?string $background = null): self
    {
        return new self($foreground, $background);
    }
}
