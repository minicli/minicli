<?php


namespace Minicli\Output\Filter;


use Minicli\Output\CLITheme;
use Minicli\Output\OutputFilterInterface;

class ColorOutputFilter implements OutputFilterInterface
{
    /** @var CLITheme */
    protected $theme;

    /**
     * ColorOutputFilter constructor.
     * @param CLITheme|null $theme If a theme is not set, the default CLITheme will be used.
     */
    public function __construct(CLITheme $theme = null)
    {
        $this->theme = $theme ?? new CLITheme();
    }

    /**
     * Gets the CLITheme
     * @return CLITheme
     */
    public function getTheme(): CLITheme
    {
        return $this->theme;
    }

    /**
     * Sets the CLITheme
     * @param CLITheme $theme
     */
    public function setTheme(CLITheme $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Filters a string according to the specified style.
     * @param string $message
     * @param string $style
     * @return string the resulting string
     */
    public function filter($message, $style = null): string
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