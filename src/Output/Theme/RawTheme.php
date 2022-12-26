<?php

namespace Minicli\Output\Theme;

use Minicli\Output\CLIThemeInterface;
use Minicli\Output\Filter\SimpleOutputFilter;

class RawTheme implements CLIThemeInterface
{
    /**
     * @inheritdoc
     */
    public function getStyle(string $style_name): array
    {
        return [null];
    }

    /**
     * @inheritdoc
     */
    public function getOutputFilter()
    {
        return new SimpleOutputFilter;
    }
}
