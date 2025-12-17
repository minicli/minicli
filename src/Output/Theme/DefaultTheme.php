<?php

declare(strict_types=1);

namespace Minicli\Output\Theme;

use Minicli\Contracts\ThemeInterface;
use Minicli\Output\CLI\Background;
use Minicli\Output\CLI\FontWeight;
use Minicli\Output\CLI\Foreground;
use Minicli\Output\ThemeConfig;
use Minicli\Output\ThemeStyle;

class DefaultTheme implements ThemeInterface
{
    public ThemeConfig $config;

    /**
     * DefaultTheme constructor.
     */
    public function __construct()
    {
        $styles = array_merge($this->getDefaultColors(), $this->themeColors());

        $formatted = [];
        foreach ($styles as $name => $style) {
            $formatted[$name] = ThemeStyle::make(...$style);
        }

        $this->config = ThemeConfig::make(...$formatted);
    }

    /**
     * Obtains the colors that compose a style for that theme, such as "error" or "success"
     */
    public function style(string $name): ThemeStyle
    {
        return $this->config->{$name} ?? $this->config->default;
    }

    /**
     * Sets a style
     */
    public function setStyle(string $name, ThemeStyle $style): void
    {
        $this->config->{$name} = $style;
    }

    /**
     * get default style colors
     *
     * @return array<string,array<int,string>>
     */
    public function getDefaultColors(): array
    {
        return [
            'default' => [Foreground::WHITE->value],
            'alt' => [Foreground::BLACK->value, Background::WHITE->value],
            'error' => [Foreground::RED->value],
            'error_alt' => [Foreground::WHITE->value, Background::RED->value],
            'success' => [Foreground::GREEN->value],
            'success_alt' => [Foreground::WHITE->value, Background::GREEN->value],
            'info' => [Foreground::CYAN->value],
            'info_alt' => [Foreground::WHITE->value, Background::CYAN->value],
            'bold' => [FontWeight::BOLD->value],
            'dim' => [FontWeight::DIM->value],
            'italic' => [FontWeight::ITALIC->value],
            'underline' => [FontWeight::UNDERLINE->value],
            'invert' => [FontWeight::INVERT->value],
        ];
    }

    /**
     * This method should be implemented by children themes to overwrite and set custom styles/colors
     *
     * @return array<string, array<int, string>>
     */
    public function themeColors(): array
    {
        return [];
    }
}
