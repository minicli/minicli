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
        $datetime = \Carbon\CarbonImmutable::now();
        $style ??= 'Y-m-d H:i:S';

        return $datetime->format("[{$style}]") . $message;
    }
}
