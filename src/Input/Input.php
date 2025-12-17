<?php

declare(strict_types=1);

namespace Minicli\Input;

final class Input
{
    /**
     * @param  array<string>  $inputHistory
     */
    public function __construct(
        private string $prompt = 'minicli$> ',
        private array $inputHistory = [],
    ) {}

    public function read(): string
    {
        $input = (string) readline($this->prompt);
        $this->inputHistory[] = $input;

        return $input;
    }

    /**
     * @return array<string>
     */
    public function getInputHistory(): array
    {
        return $this->inputHistory;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }
}
