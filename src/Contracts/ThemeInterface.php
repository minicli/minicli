<?php

declare(strict_types=1);

namespace Minicli\Contracts;

use Minicli\Output\ThemeStyle;

interface ThemeInterface
{
    /**
     * Obtains the colors that compose a style for that theme, such as "error" or "success"
     *
     * @param  string  $name  The name of the style
     */
    public function style(string $name): ThemeStyle;
}
