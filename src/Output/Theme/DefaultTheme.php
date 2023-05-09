<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLIColors;
use Minicli\Output\CLIThemeInterface;
use Minicli\Output\ThemeConfig;
use Minicli\Output\ThemeStyle;

class DefaultTheme implements CLIThemeInterface
{
    public ThemeConfig $config;

    /**
     * DefaultTheme constructor.
     */
    public function __construct()
    {
        $styles = array_merge($this->getDefaultColors(), $this->getThemeColors());

        $formatted = [];
        foreach ($styles as $name => $style) {
            $formatted[$name] = ThemeStyle::make(...$style);
        }

        $this->config = ThemeConfig::make(...$formatted);
    }

    /**
     * Obtains the colors that compose a style for that theme, such as "error" or "success"
     *
     * @param string $name
     * @return ThemeStyle
     */
    public function getStyle(string $name): ThemeStyle
    {
        return $this->config->$name ?? $this->config->default;
    }

    /**
     * Sets a style
     *
     * @param string $name
     * @param ThemeStyle $style
     */
    public function setStyle(string $name, ThemeStyle $style): void
    {
        $this->config->$name = $style;
    }

    /**
     * get default style colors
     *
     * @return array<string,array<int,string>>
     */
    public function getDefaultColors(): array
    {
        return [
            'default'     => [ CLIColors::$FG_WHITE ],
            'alt'         => [ CLIColors::$FG_BLACK, CLIColors::$BG_WHITE ],
            'error'       => [ CLIColors::$FG_RED ],
            'error_alt'   => [ CLIColors::$FG_WHITE, CLIColors::$BG_RED ],
            'success'     => [ CLIColors::$FG_GREEN ],
            'success_alt' => [ CLIColors::$FG_WHITE, CLIColors::$BG_GREEN ],
            'info'        => [ CLIColors::$FG_CYAN],
            'info_alt'    => [ CLIColors::$FG_WHITE, CLIColors::$BG_CYAN ],
            'bold'        => [ CliColors::$BOLD ],
            'dim'         => [ CliColors::$DIM ],
            'italic'      => [ CliColors::$ITALIC ],
            'underline'   => [ CliColors::$UNDERLINE ],
            'invert'      => [ CliColors::$INVERT ]
        ];
    }

    /**
     * This method should be implemented by children themes to overwrite and set custom styles/colors
     *
     * @return array
     */
    public function getThemeColors(): array
    {
        return [];
    }
}
