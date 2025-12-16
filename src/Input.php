<?php

declare(strict_types=1);

namespace Minicli;

class Input
{
    /**
     * @param  array<int, string>  $inputHistory
     */
    public function __construct(
        protected string $prompt = 'minicli$> ',
        protected array $inputHistory = [],
    ) {}

    /**
     * read input
     */
    public function read(): string
    {
        $input = (string) readline($this->getPrompt());

        $this->inputHistory[] = $input;

        return $input;
    }

    /**
     * get input history
     *
     * @return array<int, string>
     */
    public function getInputHistory(): array
    {
        return $this->inputHistory;
    }

    /**
     * get prompt
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * set prompt
     */
    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }
}
