<?php

declare(strict_types=1);

namespace Minicli\Input;

final class Input
{
    /**
     * @param  array<string>  $inputHistory
     */
    public function __construct(
        private string $prompt = '> ',
        private array $inputHistory = [],
    ) {}

    public function read(): string
    {
        $input = (string) readline($this->prompt);
        $this->inputHistory[] = $input;

        return $input;
    }

    public function readHidden(): string
    {
        if (! defined('STDIN') || ! stream_isatty(STDIN)) {
            $fallback = fgets(STDIN);
            $input = trim($fallback === false ? '' : $fallback, "\r\n");
            $this->inputHistory[] = $input;

            return $input;
        }

        if ($this->prompt !== '') {
            fwrite(STDOUT, $this->prompt);
        }

        $sttyMode = shell_exec('stty -g');
        if (! is_string($sttyMode) || $sttyMode === '') {
            $fallback = fgets(STDIN);
            $input = trim($fallback === false ? '' : $fallback, "\r\n");
            $this->inputHistory[] = $input;

            return $input;
        }

        shell_exec('stty -echo -icanon min 1 time 0');

        $input = '';

        try {
            while (true) {
                $char = fgetc(STDIN);

                if ($char === false) {
                    continue;
                }

                if ($char === "\n" || $char === "\r") {
                    break;
                }

                if ($char === "\010" || $char === "\177") {
                    if ($input !== '') {
                        $input = substr($input, 0, -1);
                        fwrite(STDOUT, "\010 \010");
                    }

                    continue;
                }

                $input .= $char;
                fwrite(STDOUT, '*');
            }
        } finally {
            shell_exec('stty ' . trim($sttyMode));
        }

        fwrite(STDOUT, PHP_EOL);
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
