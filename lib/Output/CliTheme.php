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
        return $this->styles[$name] ? $this->styles[$name] : null;
    }
}