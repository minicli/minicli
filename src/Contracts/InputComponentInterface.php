<?php

declare(strict_types=1);

namespace Minicli\Contracts;

interface InputComponentInterface
{
    /**
     * @return string|bool|int|float|array<string>
     */
    public function ask(): string|bool|int|float|array;

    public function required(): static;

    public function optional(): static;
}
