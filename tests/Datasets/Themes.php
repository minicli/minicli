<?php

declare(strict_types=1);

use Minicli\Output\Theme\DaltonTheme;
use Minicli\Output\Theme\DefaultTheme;
use Minicli\Output\Theme\DraculaTheme;
use Minicli\Output\Theme\UnicornTheme;

dataset('themes', [
    'default' => new DefaultTheme(),
    'unicorn' => new UnicornTheme(),
    'dalton' => new DaltonTheme(),
    'dracula' => new DraculaTheme(),
]);
