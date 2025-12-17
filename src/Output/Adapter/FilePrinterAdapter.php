<?php

declare(strict_types=1);

namespace Minicli\Output\Adapter;

use Minicli\Contracts\PrinterAdapterInterface;
use TypeError;

class FilePrinterAdapter implements PrinterAdapterInterface
{
    /**
     * setup file printer adapter
     */
    public function __construct(
        protected string $outputFile,
    ) {}

    /**
     * writes output to file
     *
     * @throws TypeError
     */
    public function out(string $message, ?string $style = null): string
    {
        $directory = dirname($this->outputFile);

        if (! is_writable($directory)) {
            throw new TypeError("Could not open file {$this->outputFile} for writing.");
        }

        $fp = fopen($this->outputFile, 'a+');

        if ($fp === false) {
            throw new TypeError("Could not open file {$this->outputFile} for writing.");
        }

        fwrite($fp, $message);
        fclose($fp);

        return $message;
    }
}
