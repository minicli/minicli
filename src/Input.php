<?php

declare(strict_types=1);

namespace Minicli;

class Input
{
    /**
     * @param string $prompt
     * @param array<int, string> $inputHistory
     */
    public function __construct(
        protected string $prompt = 'minicli$> ',
        protected array $inputHistory = [],
    ) {
    }

    /**
     * read input
     *
     * @return string
     */
    public function read(): string
    {
        $input = readline($this->getPrompt());
        if ($input === false) {
            return '';
        }

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
