<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Contracts\OutputFilterInterface;
use Minicli\Contracts\ThemeInterface;
use Minicli\Output\Theming\Themes\DefaultTheme;

final class ColorOutputFilter implements OutputFilterInterface
{
    private ThemeInterface $theme;

    public function __construct(?ThemeInterface $theme = null)
    {
        $this->theme = $theme ?? new DefaultTheme();
    }

    public function theme(): ThemeInterface
    {
        return $this->theme;
    }

    public function setTheme(ThemeInterface $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * Filters a string according to the specified style.
     */
    public function filter(string $message, ?string $style = 'default'): string
    {
        return $this->format($message, $style ?? 'default');
    }

    /**
     * Formats a message with color codes based on a theme
     */
    public function format(string $message, string $style = 'default'): string
    {
        $styleColors = $this->theme->style($style);

        $bg = '';
        if (! in_array($styleColors->background?->value, [null, '', '0'], true)) {
            $bg = ';' . $styleColors->background->value;
        }

        return sprintf("\e[%s%sm%s\e[0m", $styleColors->foreground->value, $bg, $message);
    }
}
