<?php

declare(strict_types=1);

namespace Minicli\Output\Adapter;

use Minicli\Output\PrinterAdapterInterface;

class FilePrinterAdapter implements PrinterAdapterInterface
{
    /**
     * output file
     *
     * @var string
     */
    protected string $outputFile;

    /**
     * setup file printer adapter
     * @param string $outputFile
     */
    public function __construct(string $outputFile)
    {
        $this->outputFile = $outputFile;
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
