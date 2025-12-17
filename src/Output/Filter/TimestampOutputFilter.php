<?php

declare(strict_types=1);

namespace Minicli\Output\Filter;

use DateTimeImmutable;
use Minicli\Contracts\OutputFilterInterface;
use Minicli\Output\Theming\StyleType;

class TimestampOutputFilter implements OutputFilterInterface
{
    public function __construct(
        private readonly string $format = 'Y-m-d H:i:s'
    ) {}

    public function filter(string $message, ?StyleType $style = null): string
    {
        $datetime = new DateTimeImmutable();

        return $datetime->format("[{$this->format}]") . $message;
    }
}
