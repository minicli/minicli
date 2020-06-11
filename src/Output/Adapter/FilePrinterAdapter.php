<?php


namespace Minicli\Output\Adapter;

use Minicli\Output\PrinterAdapterInterface;

class FilePrinterAdapter implements PrinterAdapterInterface
{
    /** @var string */
    protected $output_file;

    /**
     * FilePrinterAdapter constructor.
     * @param $output_file
     */
    public function __construct($output_file)
    {
        $this->output_file = $output_file;
    }

    /**
     * Writes output to file.
     * @param string $message
     * @param null $style
     * @return bool
     */
    public function out($message, $style = null)
    {
        $fp = fopen($this->output_file, "a+");
        fwrite($fp, $message);
        fclose($fp);

        return $message;
    }
}
