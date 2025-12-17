<?php

declare(strict_types=1);

namespace Minicli\Output\Theming;

use Minicli\Contracts\ThemeInterface;
use Minicli\Output\Filter\ColorOutputFilter;

final readonly class ThemeHelper
{
    /**
     * @param  class-string<ThemeInterface>|null  $theme
     */
    public function __construct(public ?string $theme = null) {}

    /**
     * Initialize and return an OutputFilter based on our theme class
     */
    public function getOutputFilter(): ColorOutputFilter
    {
        if ($this->theme === null || ! class_exists($this->theme)) {
            return new ColorOutputFilter();
        }

        /** @var ThemeInterface $theme */
        $theme = new $this->theme();

        return new ColorOutputFilter($theme);
    }
}
