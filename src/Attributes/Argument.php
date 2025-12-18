<?php

declare(strict_types=1);

namespace Minicli\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class Argument
{
    public function __construct(
        public string $name = '',
        public string $description = '',
    ) {}
}
