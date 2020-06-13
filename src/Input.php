<?php

namespace Minicli;

class Input
{
    /** @var array  */
    protected $input_history = [];

    /** @var string */
    protected $prompt;

    public function __construct($prompt = 'minicli$> ')
    {
        $this->setPrompt($prompt);
    }

    public function read()
    {
        $input = readline($this->getPrompt());
        $this->input_history[] = $input;

        return $input;
    }

    /**
     * @return array
     */
    public function getInputHistory()
    {
        return $this->input_history;
    }

    /**
     * @return string
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * @param string $prompt
     */
    public function setPrompt(string $prompt)
    {
        $this->prompt = $prompt;
    }
}
