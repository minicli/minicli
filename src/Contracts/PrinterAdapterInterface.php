<?php

declare(strict_types=1);

namespace Minicli\Contracts;

interface PrinterAdapterInterface
{
    public function out(string $message): string;
}
