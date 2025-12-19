<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Contracts\OutputFilterInterface;
use Minicli\Contracts\ThemeInterface;
use Minicli\Output\Theming\StyleType;
use Minicli\Output\Theming\Themes\DefaultTheme;

class ColorOutputFilter implements OutputFilterInterface
{
    protected ThemeInterface $theme;

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
     * Filters a string according to the specified styles.
     *
     * @param  array<StyleType>  $styles
     */
    public function filter(string $message, array $styles = []): string
    {
        return $this->format($message, $styles);
    }

    /**
     * Formats a message with color codes based on a theme
     *
     * @param  array<StyleType>  $styles
     */
    public function format(string $message, array $styles = []): string
    {
        if ($styles === []) {
            $styles = [StyleType::DEFAULT];
        }

        $codes = [];

        foreach ($styles as $style) {
            $styleColors = $this->theme->style($style);
            $codes[] = $styleColors->foreground->value;

            if (! in_array($styleColors->background?->value, [null, '', '0'], true)) {
                $codes[] = $styleColors->background->value;
            }
        }

        return sprintf("\e[%sm%s\e[0m", implode(';', $codes), $message);
    }
}
