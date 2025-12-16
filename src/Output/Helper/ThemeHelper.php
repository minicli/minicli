<?php

declare(strict_types=1);

namespace Minicli\Output\Helper;

use Minicli\Output\CLIThemeInterface;
use Minicli\Output\Filter\ColorOutputFilter;

class ThemeHelper
{
    /**
     * theme
     *
     * @var class-string<CLIThemeInterface>|string
     */
    protected string $theme = '';

    /**
     * ThemeHelper constructor. Takes in the App theme config value
     */
    public function __construct(string $themeConfig = '')
    {
        $this->theme = $this->parseThemeSetting($themeConfig);
    }

    /**
     * Initialize and return an OutputFilter based on our theme class
     */
    public function getOutputFilter(): ColorOutputFilter
    {
        if (class_exists($this->theme)) {
            /**
             * @var CLIThemeInterface $theme
             */
            $theme = new $this->theme();

            return new ColorOutputFilter($theme);
        }

        return new ColorOutputFilter();
    }

    /**
     * Parses the theme config setting and returns a namespaced class name.
     */
    protected function parseThemeSetting(string $themeConfig): string
    {
        if ($themeConfig === '' || $themeConfig === '0') {
            return '';
        }

        if ($themeConfig[0] === '\\') {
            return '\Minicli\Output\Theme' . $themeConfig . 'Theme';  // Built-in theme.
        }

        return $themeConfig . 'Theme'; // User-defined theme.
    }
}
