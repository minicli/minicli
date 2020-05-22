<?php


namespace Minicli\Output\Filter;


use Minicli\Output\CLITheme;
use Minicli\Output\OutputFilterInterface;

class ColorOutputFilter implements OutputFilterInterface
{
    /** @var CLITheme */
    protected $theme;

    public function __construct(CLITheme $theme = null)
    {
        $this->theme = $theme ?? new CLITheme();
    }

    /**
     * @return CLITheme
     */
    public function getTheme(): CLITheme
    {
        return $this->theme;
    }

    /**
     * @param CLITheme $theme
     */
    public function setTheme(CLITheme $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @param string $message
     * @param string $style
     * @return string
     */
    public function filter($message, $style = null): string
    {
        return $this->format($message, $style);
    }

    /**
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

        $output = sprintf("\e[%s%sm%s\e[0m", $style_colors[0], $bg, $message);

        return $output;
    }
}