<?php

declare(strict_types=1);

namespace Minicli\Components;

final class Password
{
    private readonly Text $message;

    private bool $required = true;

    public function __construct(Text|string $message)
    {
        if (is_string($message)) {
            $message = Text::make($message);
        }

        $this->message = $message;
    }

    public static function make(Text|string $message): self
    {
        return new self($message);
    }

    public function ask(): string
    {
        $this->message->render();

        $input = $this->readHiddenInput();
        if ($input === '' && $this->required) {
            Alert::make('Input cannot be empty. Please provide a value.')->warning()->render();
            LineBreak::make()->render();

            return $this->ask();
        }

        return $input;
    }

    public function required(): self
    {
        $this->required = true;

        return $this;
    }

    public function optional(): self
    {
        $this->required = false;

        return $this;
    }

    private function readHiddenInput(): string
    {
        if (! defined('STDIN') || ! stream_isatty(STDIN)) {
            $fallback = fgets(STDIN);

            return trim($fallback === false ? '' : $fallback, "\r\n");
        }

        $sttyMode = shell_exec('stty -g');
        if (! is_string($sttyMode) || $sttyMode === '') {
            $fallback = fgets(STDIN);

            return trim($fallback === false ? '' : $fallback, "\r\n");
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

        return $input;
    }
}
