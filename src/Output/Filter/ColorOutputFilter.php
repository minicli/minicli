<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Output\CLIThemeInterface;
use Minicli\Output\OutputFilterInterface;
use Minicli\Output\Theme\DefaultTheme;

class ColorOutputFilter implements OutputFilterInterface
{
    /**
     * theme
     *
     * @var CLIThemeInterface
     */
    protected CLIThemeInterface $theme;

    /**
     * ColorOutputFilter constructor
     *
     * @param CLIThemeInterface|null $theme If a theme is not set, the default CLITheme will be used.
     */
    public function __construct(CLIThemeInterface $theme = null)
    {
        $this->theme = $theme ?? new DefaultTheme();
    }

    /**
     * Gets the CLITheme
     *
     * @return CLIThemeInterface
     */
    public function getTheme(): CLIThemeInterface
    {
        return $this->theme;
    }

    /**
     * Sets the CLITheme
     *
     * @param CLIThemeInterface $theme
     * @return void
     */
    public function setTheme(CLIThemeInterface $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Filters a string according to the specified style.
     *
     * @param string $message
     * @param string|null $style
     * @return string the resulting string
     */
    public function filter(string $message, ?string $style = "default"): string
    {
        return $this->format($message, $style);
    }

    /**
     * Formats a message with color codes based on a CLITheme
     *
     * @param string $message
     * @param string $style
     * @return string
     */
    public function format(string $message, string $style = "default"): string
    {
        $styleColors = $this->theme->getStyle($style);

        $bg = '';
        if (isset($styleColors[1])) {
            $bg = ';' . $styleColors[1];
        }

        return sprintf("\e[%s%sm%s\e[0m", $styleColors[0], $bg, $message);
    }
}
