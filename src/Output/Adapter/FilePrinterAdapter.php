<?php

declare(strict_types=1);

namespace Minicli\Output\Adapter;

use Minicli\Contracts\PrinterAdapterInterface;
use RuntimeException;

class FilePrinterAdapter implements PrinterAdapterInterface
{
    public function __construct(protected string $outputFile) {}

    /**
     * @throws RuntimeException
     */
    public function out(string $message, ?string $style = null): string
    {
        $directory = dirname($this->outputFile);

        if (! is_writable($directory)) {
            throw new RuntimeException("Could not open file {$this->outputFile} for writing.");
        }

        $fp = fopen($this->outputFile, 'a+');

        if ($fp === false) {
            throw new RuntimeException("Could not open file {$this->outputFile} for writing.");
        }

        fwrite($fp, $message);
        fclose($fp);

        return $message;
    }
}
