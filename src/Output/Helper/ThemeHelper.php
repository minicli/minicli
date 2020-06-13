<?php

namespace Minicli\Output\Helper;

use Minicli\Output\Filter\ColorOutputFilter;

class ThemeHelper
{
    /** @var array */
    protected $theme = '';

    /**
     * ThemeHelper constructor. Takes in the App theme config value.
     * @param string $theme_config
     */
    public function __construct(string $theme_config = '')
    {
        $this->theme = $this->parseThemeSetting($theme_config);
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
    protected function parseThemeSetting($theme_config)
    {
        if (!$theme_config) {
            return '';
        }

        if ($theme_config[0] == '\\') {
            return '\Minicli\Output\Theme' . $theme_config . 'Theme';  // Built-in theme.
        }

        return $theme_config . 'Theme'; // User-defined theme.
    }
}
