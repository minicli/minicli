<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Output\OutputFilterInterface;

class TimestampOutputFilter implements OutputFilterInterface
{
    /**
     * adds timestamp to the message
     *
     * @param string $message
     * @param string|null $style
     * @return string
     */
    public function filter(string $message, ?string $style = null): string
    {
        $datetime = new \DateTime();
        return $datetime->format('[Y-m-d H:i:S]') . $message;
    }
}
