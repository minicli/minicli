<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Output\CLIColors;
use Minicli\Output\CLIThemeInterface;

class DefaultTheme implements CLIThemeInterface
{
    /**
     * styles
     *
     * @var array
     */
    public array $styles = [];

    /**
     * DefaultTheme constructor.
     */
    public function __construct()
    {
        $styles = array_merge($this->getDefaultColors(), $this->getThemeColors());

        foreach ($styles as $name => $style) {
            $this->setStyle($name, $style);
        }
    }

    /**
     * Obtains the colors that compose a style for that theme, such as "error" or "success"
     *
     * @param string $name
     * @return array An array containing FG color and optionally BG color
     */
    public function getStyle(string $name): array
    {
        return $this->styles[$name] ?? $this->styles['default'];
    }

    /**
     * Sets a style
     *
     * @param string $name
     * @param array $style
     */
    public function setStyle(string $name, array $style): void
    {
        $this->styles[$name] = $style;
    }

    /**
     * get default style colors
     *
     * @return array
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
