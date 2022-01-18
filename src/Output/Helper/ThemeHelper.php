<?php

namespace Minicli\Output\Helper;

use Minicli\Output\Filter\ColorOutputFilter;

class ThemeHelper
{
    /** @var array */
    protected $theme = '';

    /**
     * ThemeHelper constructor. Takes in the App theme config value.
     * @param string $themeConfig
     */
    public function __construct(string $themeConfig = '')
    {
        $this->theme = $this->parseThemeSetting($themeConfig);
        return $this;
    }

    /**
     * Initialize and return an OutputFilter based on our theme class.
     * @return ColorOutPutFilter
     */
    public function getOutputFilter()
    {
        if (class_exists($this->theme)) {
            return new ColorOutputFilter(new $this->theme());
        }

        return new ColorOutputFilter();
    }

    /**
     * Parses the theme config setting and returns a namespaced class name.
     * @return string
     */
    protected function parseThemeSetting($themeConfig)
    {
        if (!$themeConfig) {
            return '';
        }

        if ($themeConfig[0] == '\\') {
            return '\Minicli\Output\Theme' . $themeConfig . 'Theme';  // Built-in theme.
        }

        return $themeConfig . 'Theme'; // User-defined theme.
    }
}
