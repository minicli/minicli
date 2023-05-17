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
        $input = (string) $this->detectAndReturnPrompt();

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
    

    /**
     * Blah
     *
     * @return string
     * @throws \Exception
     */
    public function detectAndReturnPrompt(): string
    {
        return match (true) {
            null !== shell_exec("command -v read")  => $this->useShellPrompt(),
            extension_loaded("readline") => $this->useReadlinePrompt(),
            default => throw new \Exception('Unexpected match value'),
        };
    }

    /**
     * Blah
     *
     * @return string
     */
    private function useReadlinePrompt(): string
    {
        return readline($this->getPrompt());
    }

    /**
     * Blah
     *
     * @return string
     */
    private function useShellPrompt(): string
    {
        return shell_exec('read -rp ' . escapeshellarg($this->getPrompt()) . ' input; echo $input');
    }
}
