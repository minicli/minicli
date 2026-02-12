<?php

declare(strict_types=1);

namespace Minicli\Config;

use Minicli\Attributes\Config;
use Minicli\Contracts\ThemeInterface;
use Minicli\Output\Theming\Themes\DefaultTheme;

#[Config('app')]
final readonly class AppConfig
{
    public function __construct(
        public string $name = <<< 'APPNAME'

███╗   ███╗██╗███╗   ██╗██╗ ██████╗██╗     ██╗
████╗ ████║██║████╗  ██║██║██╔════╝██║     ██║
██╔████╔██║██║██╔██╗ ██║██║██║     ██║     ██║
██║╚██╔╝██║██║██║╚██╗██║██║██║     ██║     ██║
██║ ╚═╝ ██║██║██║ ╚████║██║╚██████╗███████╗██║
╚═╝     ╚═╝╚═╝╚═╝  ╚═══╝╚═╝ ╚═════╝╚══════╝╚═╝

Minimalist, dependency-free framework for building CLI-centric PHP applications
APPNAME,
        /** @var array<string> $commandPaths */
        public array $commandPaths = [],
        /** @var class-string<ThemeInterface> */
        public string $theme = DefaultTheme::class,
        public bool $debug = true,
    ) {}
}
