<?php


namespace Minicli\Output\Filter;

use Minicli\Output\CLIThemeInterface;
use Minicli\Output\OutputFilterInterface;
use Minicli\Output\Theme\DefaultTheme;

class ColorOutputFilter implements OutputFilterInterface
{
    /** @var CLIThemeInterface */
    protected $theme;

    /**
     * ColorOutputFilter constructor.
     * @param CLIThemeInterface|null $theme If a theme is not set, the default CLITheme will be used.
     */
    public function __construct(CLIThemeInterface $theme = null)
    {
        $this->theme = $theme ?? new DefaultTheme();
    }

    /**
     * Gets the CLITheme
     * @return CLIThemeInterface
     */
    public function getTheme(): CLIThemeInterface
    {
        return $this->theme;
    }

    /**
     * Sets the CLITheme
     * @param CLIThemeInterface $theme
     */
    public function setTheme(CLIThemeInterface $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Filters a string according to the specified style.
     * @param string $message
     * @param string $style
     * @return string the resulting string
     */
    public function filter($message, $style = "default"): string
    {
        return $this->format($message, $style);
    }

    /**
     * Formats a message with color codes based on a CLITheme
     * @param string $message
     * @param string $style
     * @return string
     */
    public function format($message, $style = "default"): string
    {
        $style_colors = $this->theme->getStyle($style);

        $bg = '';
        if (isset($style_colors[1])) {
            $bg = ';' . $style_colors[1];
        }

        return sprintf("\e[%s%sm%s\e[0m", $style_colors[0], $bg, $message);
    }
}
