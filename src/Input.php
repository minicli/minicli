<?php

declare(strict_types=1);

namespace Minicli;

class Input
{
    /**
     * input history
     *
     * @var array
     */
    protected array $inputHistory = [];

    /**
     * prompt
     *
     * @var string
     */
    protected string $prompt;

    /**
     * constructor
     *
     * @param string $prompt
     */
    public function __construct($prompt = 'minicli$> ')
    {
        $this->setPrompt($prompt);
    }

    /**
     * read input
     *
     * @return string
     */
    public function read(): string
    {
        $input = readline($this->getPrompt());
        $this->inputHistory[] = $input;

        return $input;
    }

    /**
     * get input history
     *
     * @return array
     */
    public function getInputHistory(): array
    {
        return $this->inputHistory;
    }

    /**
     * get prompt
     *
     * @return string
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * set prompt
     *
     * @param string $prompt
     * @return void
     */
    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }
}
