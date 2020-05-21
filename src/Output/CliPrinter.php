<?php

namespace Minicli\Output;

use Minicli\App;
use Minicli\Config;
use Minicli\Output\Theme\DefaultCliTheme;
use Minicli\OutputInterface;
use Minicli\ServiceInterface;

class CliPrinter implements OutputInterface, ServiceInterface
{
    /** @var  CliThemeInterface */
    public $theme;

    public function __construct()
    {
        $this->setTheme(new DefaultCliTheme());
    }

    public function load(App $app)
    {
        //
    }

    /**
     * @param CliThemeInterface $theme
     */
    public function setTheme(CliThemeInterface $theme)
    {
        $this->theme = $theme;
    }
    
    public function newline()
    {
        $this->out("\n");
    }

    /**
     * @param string $message
     * @return void
     */
    public function display($message)
    {
        $this->newline();
        $this->out($message);
        $this->newline();
        $this->newline();
    }

    /**
     * @param string $message
     * @param string $style
     * @return string
     */
    public function format($message, $style = "default")
    {
        $style_colors = $this->theme->$style;

        $bg = '';
        if (isset($style_colors[1])) {
            $bg = ';' . $style_colors[1];
        }

        $output = sprintf("\e[%s%sm%s\e[0m", $style_colors[0], $bg, $message);

        return $output;
    }

    /**
     * @param string $message
     * @param string $style One of the available styles
     * @return void
     */
    public function out($message, $style = "default")
    {
        echo $this->format($message, $style);
    }

    /**
     * @param string $message
     * @return void
     */
    public function error($message)
    {
        $this->newline();
        $this->out($message, "error");
        $this->newline();
    }

    /**
     * @param string $message
     * @return void
     */
    public function info($message)
    {
        $this->newline();
        $this->out($message, "info");
        $this->newline();
    }

    /**
     * @param string $message
     * @return void
     */
    public function success($message)
    {
        $this->newline();
        $this->out($message, "success");
        $this->newline();
    }

    /**
     * @param array $table
     * @param int $min_col_size
     * @param bool $with_header
     */
    public function printTable(array $table, $min_col_size = 10, $with_header = true, $spacing = true)
    {
        $first = true;

        if ($spacing) {
            $this->newline();
        }

        foreach ($table as $index => $row) {

            $style = "default";
            if ($first && $with_header) {
                $style = "info_alt";
            }

            $this->printRow($table, $index, $style, $min_col_size);
            $first = false;
        }

        if ($spacing) {
            $this->newline();
        }
    }

    /**
     * @param array $table
     * @param int $row
     * @param string $style
     * @param int $min_col_size
     */
    public function printRow(array $table, $row, $style = "default", $min_col_size = 5)
    {

        foreach ($table[$row] as $column => $table_cell) {
            $col_size = $this->calculateColumnSize($column, $table, $min_col_size);

            $this->printCell($table_cell, $style, $col_size);
        }

        $this->out("\n");
    }

    /**
     * @param string $table_cell
     * @param string $style
     * @param int $col_size
     */
    protected function printCell($table_cell, $style = "default", $col_size = 5)
    {
        $table_cell = str_pad($table_cell, $col_size);
        $this->out($table_cell, $style);
    }

    /**
     * @param $column
     * @param array $table
     * @param int $min_col_size
     * @return int
     */
    protected function calculateColumnSize($column, array $table, $min_col_size = 5)
    {
        $size = $min_col_size;

        foreach ($table as $row) {
            $size = strlen($row[$column]) > $size ? strlen($row[$column]) + 2 : $size;
        }

        return $size;
    }
}