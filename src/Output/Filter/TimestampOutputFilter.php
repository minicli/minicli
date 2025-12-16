<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use Minicli\Output\OutputFilterInterface;

class TimestampOutputFilter implements OutputFilterInterface
{
    /**
     * adds timestamp to the message
     */
    public function filter(string $message, ?string $style = null): string
    {
        $datetime = new \DateTimeImmutable();
        $style ??= 'Y-m-d H:i:s';

        return $datetime->format("[{$style}]") . $message;
    }
}
