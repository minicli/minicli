<?php

declare(strict_types=1);

use Minicli\Attributes\Config;

#[Config('app')]
final readonly class TestAppConfig
{
    public function __construct(
        public string $name = 'Configured Test App',
        /** @var array<string> */
        public array $commandPaths = [
            __DIR__ . '/../app/Commands',
        ],
        public string $theme = Assets\Theme\CustomTheme::class,
        public bool $debug = true,
    ) {}
}
