<?php

declare(strict_types=1);

namespace Minicli\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Config
{
    public function __construct(public string $name) {}
}
