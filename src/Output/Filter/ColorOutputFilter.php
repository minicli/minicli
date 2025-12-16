<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Contracts\ThemeInterface;
use Minicli\Output\OutputFilterInterface;
use Minicli\Output\Theme\DefaultTheme;

class ColorOutputFilter implements OutputFilterInterface
{
    protected ThemeInterface $theme;

    /**
     * ColorOutputFilter constructor
     *
     * @param  ThemeInterface|null  $theme  If a theme is not set, the default CLITheme will be used.
     */
    public function __construct(?ThemeInterface $theme = null)
    {
        $this->theme = $theme ?? new DefaultTheme();
    }

    /**
     * Gets the CLITheme
     */
    public function getTheme(): ThemeInterface
    {
        return $this->theme;
    }

    /**
     * Sets the CLITheme
     */
    public function setTheme(ThemeInterface $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Filters a string according to the specified style.
     *
     * @return string the resulting string
     */
    public function filter(string $message, ?string $style = 'default'): string
    {
        return $this->format($message, $style ?? 'default');
    }

    /**
     * Formats a message with color codes based on a CLITheme
     */
    public function format(string $message, string $style = 'default'): string
    {
        $styleColors = $this->theme->style($style);

        $bg = '';
        if (! in_array($styleColors->background, [null, '', '0'], true)) {
            $bg = ';' . $styleColors->background;
        }

        return sprintf("\e[%s%sm%s\e[0m", $styleColors->foreground, $bg, $message);
    }
}
