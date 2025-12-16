<?php

declare(strict_types=1);

namespace Minicli\Console;

use Closure;

final readonly class CommandInfo
{
    public function __construct(
        public Closure $callable,
        public string $name,
        public string $description = '',
        public ?CommandInfo $parent = null,
    ) {}
}
