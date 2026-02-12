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
        return $this->storeInput((string) readline($this->prompt));
    }

    public function readHidden(): string
    {
        if (! $this->isInteractiveInput()) {
            return $this->storeInput($this->readFromStdin());
        }

        if ($this->prompt !== '') {
            fwrite(STDOUT, $this->prompt);
        }

        $sttyMode = $this->getSttyMode();
        if ($sttyMode === null) {
            return $this->storeInput($this->readFromStdin());
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
            $this->restoreSttyMode($sttyMode);
        }

        fwrite(STDOUT, PHP_EOL);

        return $this->storeInput($input);
    }

    /**
     * @param  array<string>  $options
     */
    public function readChoice(array $options, int $selectedIndex = 0): int
    {
        if ($options === []) {
            return 0;
        }

        $selectedIndex = $this->normalizeChoiceIndex($selectedIndex, $options);

        if (! $this->isInteractiveInput()) {
            $selectedIndex = $this->resolveChoiceIndex($this->readFromStdin(), $options, $selectedIndex);
            $this->storeInput($options[$selectedIndex]);

            return $selectedIndex;
        }

        $this->hideCursor();
        $this->renderChoices($options, $selectedIndex);

        $sttyMode = $this->getSttyMode();
        if ($sttyMode === null) {
            fwrite(STDOUT, PHP_EOL);
            $selectedIndex = $this->resolveChoiceIndex($this->readFromStdin(), $options, $selectedIndex);
            $this->storeInput($options[$selectedIndex]);
            $this->showCursor();

            return $selectedIndex;
        }

        shell_exec('stty -echo -icanon min 1 time 0');

        try {
            while (true) {
                $char = fgetc(STDIN);

                if ($char === false) {
                    continue;
                }

                if ($char === "\n" || $char === "\r") {
                    break;
                }

                if ($char === "\t") {
                    $selectedIndex = $this->moveChoiceIndex($selectedIndex, 1, $options);
                    $this->renderChoices($options, $selectedIndex);

                    continue;
                }

                if ($char === "\033") {
                    $sequenceOne = fgetc(STDIN);
                    $sequenceTwo = fgetc(STDIN);
                    if ($sequenceOne !== '[') {
                        continue;
                    }
                    if ($sequenceTwo === false) {
                        continue;
                    }

                    if ($sequenceTwo === 'C' || $sequenceTwo === 'B') {
                        $selectedIndex = $this->moveChoiceIndex($selectedIndex, 1, $options);
                        $this->renderChoices($options, $selectedIndex);
                    }

                    if ($sequenceTwo === 'D' || $sequenceTwo === 'A') {
                        $selectedIndex = $this->moveChoiceIndex($selectedIndex, -1, $options);
                        $this->renderChoices($options, $selectedIndex);
                    }

                    continue;
                }

                if ($char === 'h' || $char === 'k') {
                    $selectedIndex = $this->moveChoiceIndex($selectedIndex, -1, $options);
                    $this->renderChoices($options, $selectedIndex);

                    continue;
                }

                if ($char === 'l' || $char === 'j') {
                    $selectedIndex = $this->moveChoiceIndex($selectedIndex, 1, $options);
                    $this->renderChoices($options, $selectedIndex);

                    continue;
                }
            }
        } finally {
            $this->restoreSttyMode($sttyMode);
            $this->showCursor();
        }

        fwrite(STDOUT, PHP_EOL);
        $this->storeInput($options[$selectedIndex]);

        return $selectedIndex;
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

    /**
     * @param  array<string>  $options
     */
    private function renderChoices(array $options, int $selectedIndex): void
    {
        $formatted = [];

        foreach ($options as $index => $option) {
            $marker = $index === $selectedIndex ? '(*)' : '( )';
            $formatted[] = "{$marker} {$option}";
        }

        fwrite(STDOUT, "\r\033[2K" . implode('   ', $formatted));
    }

    private function isInteractiveInput(): bool
    {
        return defined('STDIN') && stream_isatty(STDIN);
    }

    private function getSttyMode(): ?string
    {
        $mode = shell_exec('stty -g');

        if (! is_string($mode) || $mode === '') {
            return null;
        }

        return trim($mode);
    }

    private function restoreSttyMode(string $mode): void
    {
        shell_exec('stty ' . $mode);
    }

    private function readFromStdin(): string
    {
        $line = fgets(STDIN);

        return trim($line === false ? '' : $line, "\r\n");
    }

    private function storeInput(string $input): string
    {
        $this->inputHistory[] = $input;

        return $input;
    }

    /**
     * @param  array<string>  $options
     */
    private function normalizeChoiceIndex(int $index, array $options): int
    {
        return max(0, min($index, count($options) - 1));
    }

    /**
     * @param  array<string>  $options
     */
    private function resolveChoiceIndex(string $input, array $options, int $currentIndex): int
    {
        if ($input === '') {
            return $currentIndex;
        }

        if (ctype_digit($input)) {
            $numericIndex = (int) $input - 1;
            if ($numericIndex >= 0 && $numericIndex < count($options)) {
                return $numericIndex;
            }
        }

        foreach ($options as $index => $option) {
            if (strcasecmp($option, $input) === 0) {
                return $index;
            }
        }

        return $currentIndex;
    }

    /**
     * @param  array<string>  $options
     */
    private function moveChoiceIndex(int $currentIndex, int $direction, array $options): int
    {
        $count = count($options);

        return ($currentIndex + $direction + $count) % $count;
    }

    private function hideCursor(): void
    {
        fwrite(STDOUT, "\033[?25l");
    }

    private function showCursor(): void
    {
        fwrite(STDOUT, "\033[?25h");
    }
}
