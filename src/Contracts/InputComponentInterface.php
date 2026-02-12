<?php

declare(strict_types=1);

namespace Minicli\Contracts;

interface InputComponentInterface
{
    /**
     * @return string|bool|array<string>
     */
    public function ask(): string|bool|array;

    public function required(): static;

    public function optional(): static;
}
