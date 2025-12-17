<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Minicli\Output\Theming\StyleType;
use Minicli\Output\Theming\ThemeStyle;

interface ThemeInterface
{
    /**
     * Obtains the colors that compose a style for that theme
     */
    public function style(StyleType $name): ThemeStyle;
}
