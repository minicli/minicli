<?php


namespace Minicli\Output\Filter;

use Minicli\Output\OutputFilterInterface;

class TimestampOutputFilter implements OutputFilterInterface
{
    /**
     * Adds timestamp to the message
     * @param string $message
     * @param null $style
     * @return string
     */
    public function filter($message, $style = null)
    {
        $datetime = new \DateTime();
        return $datetime->format('[Y-m-d H:i:S]') . $message;
    }
}
