<?php

declare(strict_types=1);

namespace Minicli\Output\Theming\Themes;

use Minicli\Contracts\ThemeInterface;
use Minicli\Output\Theming\Background;
use Minicli\Output\Theming\FontWeight;
use Minicli\Output\Theming\Foreground;
use Minicli\Output\Theming\StyleType;
use Minicli\Output\Theming\ThemeConfig;
use Minicli\Output\Theming\ThemeStyle;

class DefaultTheme implements ThemeInterface
{
    public ThemeConfig $config;

    public function __construct()
    {
        $defaultTheme = $this->defaultConfig();
        $customTheme = $this->themeConfig();

        $this->config = ThemeConfig::make(
            default: $customTheme->default ?? $defaultTheme->default,
            alt: $customTheme->alt ?? $defaultTheme->alt,
            error: $customTheme->error ?? $defaultTheme->error,
            error_alt: $customTheme->error_alt ?? $defaultTheme->error_alt,
            success: $customTheme->success ?? $defaultTheme->success,
            success_alt: $customTheme->success_alt ?? $defaultTheme->success_alt,
            info: $customTheme->info ?? $defaultTheme->info,
            info_alt: $customTheme->info_alt ?? $defaultTheme->info_alt,
            bold: $customTheme->bold ?? $defaultTheme->bold,
            dim: $customTheme->dim ?? $defaultTheme->dim,
            italic: $customTheme->italic ?? $defaultTheme->italic,
            underline: $customTheme->underline ?? $defaultTheme->underline,
            invert: $customTheme->invert ?? $defaultTheme->invert,
        );
    }

    /**
     * Obtains the colors that compose a style for that theme
     */
    public function style(StyleType $name): ThemeStyle
    {
        /** @var ThemeStyle $default */
        $default = $this->config->default;

        return $this->config->{$name->value} ?? $default;
    }

    public function setStyle(StyleType $name, ThemeStyle $style): void
    {
        $this->config->{$name->value} = $style;
    }

    public function defaultConfig(): ThemeConfig
    {
        return ThemeConfig::make(
            default: ThemeStyle::make(Foreground::WHITE),
            alt: ThemeStyle::make(Foreground::BLACK, Background::WHITE),
            error: ThemeStyle::make(Foreground::RED),
            error_alt: ThemeStyle::make(Foreground::WHITE, Background::RED),
            success: ThemeStyle::make(Foreground::GREEN),
            success_alt: ThemeStyle::make(Foreground::WHITE, Background::GREEN),
            info: ThemeStyle::make(Foreground::CYAN),
            info_alt: ThemeStyle::make(Foreground::WHITE, Background::CYAN),
            bold: ThemeStyle::make(FontWeight::BOLD),
            dim: ThemeStyle::make(FontWeight::DIM),
            italic: ThemeStyle::make(FontWeight::ITALIC),
            underline: ThemeStyle::make(FontWeight::UNDERLINE),
            invert: ThemeStyle::make(FontWeight::INVERT),
        );
    }

    /**
     * This method should be implemented by children themes to overwrite and set custom styles/colors
     */
    public function themeConfig(): ThemeConfig
    {
        return ThemeConfig::make();
    }
}
