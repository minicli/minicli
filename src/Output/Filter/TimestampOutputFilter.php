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

    /**
     * @param  array<StyleType>  $formats
     */
    public function filter(string $message, ?StyleType $style = null, array $formats = []): string
    {
        $datetime = new DateTimeImmutable();

        return $datetime->format("[{$this->format}]") . $message;
    }
}
