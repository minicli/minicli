<?php

declare(strict_types=1);

namespace Minicli\Contracts;

interface InputComponentInterface
{
    /**
     * @return string|bool|int|float|array<string>
     */
    public function ask(): string|bool|int|float|array;

    /**
     * @param  callable(string|bool|int|float|array<mixed>): ?string  $callback
     */
    public function validate(callable $callback): static;

    public function required(): static;

    public function optional(): static;
}
