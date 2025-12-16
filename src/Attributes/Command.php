<?php

declare(strict_types=1);

namespace Minicli\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class Command
{
    public function __construct(
        public string $name,
        public string $description = '',
        public bool $default = false,
    ) {}
}
