<?php

declare(strict_types=1);

namespace Minicli\Output;

trait PrinterProxy
{
    /**
     * Prints a content using configured filters
     *
     * @param string $content
     * @param string $style
     */
    protected function out(string $content, string $style = "default"): void
    {
        $this->getPrinter()->out($content, $style);
    }

    /**
     * Prints content without formatting or styling
     *
     * @param string $content
     * @return void
     */
    protected function rawOutput(string $content): void
    {
        $this->getPrinter()->rawOutput($content);
    }

    /**
     * prints a new line
     *
     * @return void
     */
    protected function newline(): void
    {
        $this->getPrinter()->newline();
    }

    /**
     * Print the output with a newline either side.
     *
     * @param string $content
     * @param string $style
     * @return void
     */
    protected function breathe(string $content, string $style): void
    {
        $this->getPrinter()->breathe($content, $style);
    }

    /**
     * Displays content using the "default" style
     *
     * @param string $content
     * @param bool $alt  Use the inverted style ("alt")
     * @return void
     */
    protected function display(string $content, bool $alt = false): void
    {
        $this->getPrinter()->display($content, $alt);
    }

    /**
     * Prints content using the "error" style
     *
     * @param string $content
     * @param bool $alt Use the inverted style ("error_alt")
     * @return void
     */
    protected function error(string $content, bool $alt = false): void
    {
        $this->getPrinter()->error($content, $alt);
    }

    /**
     * Prints content using the "info" style
     *
     * @param string $content
     * @param bool $alt Use the inverted style ("info_alt")
     * @return void
     */
    protected function info(string $content, bool $alt = false): void
    {
        $this->getPrinter()->info($content, $alt);
    }

    /**
     * Prints content using the "success" style
     *
     * @param string $content The string to print
     * @param bool $alt Use the inverted style ("success_alt")
     * @return void
     */
    protected function success(string $content, bool $alt = false): void
    {
        $this->getPrinter()->success($content, $alt);
    }

    /**
     * Shortcut method to print tables using the TableHelper
     *
     * @param array<int, array<string>> $table An array containing all table rows. Each row must be an array with the individual cells.
     */
    protected function printTable(array $table): void
    {
        $this->getPrinter()->printTable($table);
    }

    /**
     * Ask the users input
     *
     * @param string $content
     * @param string $method
     * @return string
     */
    protected function ask(string $content, string $method = 'display'): string
    {
        return $this->getPrinter()->ask($content, $method);
    }
}
