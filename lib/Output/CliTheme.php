<?php

namespace Minicli\Output;


class CliTheme
{
    public $styles = [];

    public function __construct(array $styles)
    {
        $this->styles = $styles;
    }

    public function __get($name)
    {
        return array_key_exists($name, $this->styles) ? $this->styles[$name] : null;
    }
}
