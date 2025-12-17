<?php

declare(strict_types=1);

namespace Minicli\Output\Theming;

final readonly class ThemeStyle
{
    public function __construct(
        public Foreground|FontWeight $foreground,
        public ?Background $background = null
    ) {}

    public static function make(Foreground|FontWeight $foreground, ?Background $background = null): self
    {
        return new self($foreground, $background);
    }
}
