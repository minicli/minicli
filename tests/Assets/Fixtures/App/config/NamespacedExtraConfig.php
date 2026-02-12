<?php

declare(strict_types=1);

namespace Assets\Fixtures\App\Config;

use Minicli\Attributes\Config;

#[Config('extra')]
final readonly class NamespacedExtraConfig
{
    public function __construct(
        public string $value = 'loaded',
    ) {}
}
