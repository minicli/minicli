<?php

declare(strict_types=1);

namespace Minicli\Output\Adapter;

use Minicli\Output\PrinterAdapterInterface;

class FilePrinterAdapter implements PrinterAdapterInterface
{
    /**
     * setup file printer adapter
     * @param string $outputFile
     */
    public function __construct(
        protected string $outputFile,
    ) {
    }

    /**
     * writes output to file
     *
     * @param string $message
     * @param string|null $style
     * @return string
     */
    public function out(string $message, ?string $style = null): string
    {
        $fp = fopen($this->outputFile, "a+");
        fwrite($fp, $message);
        fclose($fp);

        return $message;
    }
}
