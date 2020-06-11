<?php


namespace Minicli\Output;

interface OutputFilterInterface
{
    public function filter($message, $style = null);
}
