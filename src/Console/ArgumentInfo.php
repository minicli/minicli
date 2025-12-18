<?php

declare(strict_types=1);

namespace Minicli\Console;

final readonly class ArgumentInfo
{
    public function __construct(
        public string $name,
        public string $description,
        public bool $required,
    ) {}
}
