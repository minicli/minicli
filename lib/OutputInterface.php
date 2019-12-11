<?php

namespace Minicli;


interface OutputInterface
{
    /**
     * Prints a message (no linebreak).
     * @param string $message
     * @return mixed
     */
    public function out($message);

    /**
     * Prints a line break.
     * @return void
     */
    public function newline();

    /**
     * Displays a message wrapped in new lines.
     * @param $message
     * @return void
     */
    public function display($message);
}