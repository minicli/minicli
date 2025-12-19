<?php

declare(strict_types=1);

namespace Minicli\Components;

class LineBreak extends Component
{
    public function __construct(
        private readonly int $amount = 1,
    ) {}

    public static function make(int $amount = 1): self
    {
        return new self($amount);
    }

    public function output(): string
    {
        return str_repeat("\n", $this->amount);
    }
}
