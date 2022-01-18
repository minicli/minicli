<?php


namespace Minicli\Output\Adapter;

use Minicli\Output\PrinterAdapterInterface;

class FilePrinterAdapter implements PrinterAdapterInterface
{
    /** @var string */
    protected $outputFile;

    /**
     * FilePrinterAdapter constructor.
     * @param $outputFile
     */
    public function __construct($outputFile)
    {
        $this->outputFile = $outputFile;
    }

    /**
     * Writes output to file.
     * @param string $message
     * @param null $style
     * @return bool
     */
    public function out($message, $style = null)
    {
        $fp = fopen($this->outputFile, "a+");
        fwrite($fp, $message);
        fclose($fp);

        return $message;
    }
}
