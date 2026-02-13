<?php

declare(strict_types=1);

namespace Minicli\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final readonly class Middleware
{
    /**
     * @param  array<class-string>  $middlewares
     */
    public function __construct(
        public array $middlewares,
    ) {}
}
