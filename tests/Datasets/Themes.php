<?php

declare(strict_types=1);

use Minicli\Output\Theming\Themes\DaltonTheme;
use Minicli\Output\Theming\Themes\DefaultTheme;
use Minicli\Output\Theming\Themes\DraculaTheme;
use Minicli\Output\Theming\Themes\UnicornTheme;

dataset('themes', [
    'default' => new DefaultTheme(),
    'unicorn' => new UnicornTheme(),
    'dalton' => new DaltonTheme(),
    'dracula' => new DraculaTheme(),
]);
