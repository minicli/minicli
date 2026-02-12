<?php

declare(strict_types=1);

namespace Minicli\Contracts;

interface InputComponentInterface
{
    public function ask(): string;

    public function required(): static;

    public function optional(): static;
}
