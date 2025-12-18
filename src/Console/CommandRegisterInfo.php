<?php

declare(strict_types=1);

namespace Minicli\Console;

final readonly class CommandRegisterInfo
{
    public function __construct(
        public string $name,
        public string $description,
    ) {}
}
