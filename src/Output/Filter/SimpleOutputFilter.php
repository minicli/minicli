<?php


namespace Minicli\Output\Filter;

use Minicli\Output\OutputFilterInterface;

class SimpleOutputFilter implements OutputFilterInterface
{
    public function filter($message, $style = null)
    {
        return $message;
    }
}
