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

    public function readNumber(
        int|float|null $defaultValue = null,
        int|float $step = 1,
        int|float|null $minValue = null,
        int|float|null $maxValue = null,
    ): string {
        if (! $this->isInteractiveInput()) {
            $input = $this->readFromStdin();
            if ($input === '' && $defaultValue !== null) {
                $input = $this->stringifyNumber($this->clampNumber($defaultValue, $minValue, $maxValue));
            }

            return $this->storeInput($input);
        }

        $sttyMode = $this->getSttyMode();
        if ($sttyMode === null) {
            $input = $this->readFromStdin();
            if ($input === '' && $defaultValue !== null) {
                $input = $this->stringifyNumber($this->clampNumber($defaultValue, $minValue, $maxValue));
            }

            return $this->storeInput($input);
        }

        $input = $defaultValue !== null
            ? $this->stringifyNumber($this->clampNumber($defaultValue, $minValue, $maxValue))
            : '';

        shell_exec('stty -echo -icanon min 1 time 0');
        $this->renderCurrentNumberInput($input);

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
                        $this->renderCurrentNumberInput($input);
                    }

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

                    if ($sequenceTwo === 'A') {
                        $input = $this->adjustNumericInput($input, abs($step), $defaultValue, $minValue, $maxValue);
                        $this->renderCurrentNumberInput($input);
                    }

                    if ($sequenceTwo === 'B') {
                        $input = $this->adjustNumericInput($input, -abs($step), $defaultValue, $minValue, $maxValue);
                        $this->renderCurrentNumberInput($input);
                    }

                    continue;
                }

                if (in_array($char, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', '-', '+'], true)) {
                    $input .= $char;
                    $this->renderCurrentNumberInput($input);
                }
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
    public function readChoice(array $options, int $selectedIndex = 0, bool $vertical = false): int
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
        $this->renderChoices($options, $selectedIndex, $vertical);

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
                    $this->renderChoices($options, $selectedIndex, $vertical, true);

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
                        $this->renderChoices($options, $selectedIndex, $vertical, true);
                    }

                    if ($sequenceTwo === 'D' || $sequenceTwo === 'A') {
                        $selectedIndex = $this->moveChoiceIndex($selectedIndex, -1, $options);
                        $this->renderChoices($options, $selectedIndex, $vertical, true);
                    }

                    continue;
                }

                if ($char === 'h' || $char === 'k') {
                    $selectedIndex = $this->moveChoiceIndex($selectedIndex, -1, $options);
                    $this->renderChoices($options, $selectedIndex, $vertical, true);

                    continue;
                }

                if ($char === 'l' || $char === 'j') {
                    $selectedIndex = $this->moveChoiceIndex($selectedIndex, 1, $options);
                    $this->renderChoices($options, $selectedIndex, $vertical, true);

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
     * @param  array<string>  $options
     * @param  array<int>  $selectedIndices
     * @return array<int>
     */
    public function readMultiChoice(array $options, array $selectedIndices = [], int $activeIndex = 0, bool $vertical = false): array
    {
        if ($options === []) {
            return [];
        }

        $activeIndex = $this->normalizeChoiceIndex($activeIndex, $options);
        $selected = $this->normalizeSelectedIndices($selectedIndices, $options);

        if (! $this->isInteractiveInput()) {
            $line = $this->readFromStdin();
            if ($line !== '') {
                $selected = $this->resolveMultiChoiceIndices($line, $options, $selected);
            }

            $this->storeInput(implode(',', array_map(
                static fn (int $index): string => $options[$index],
                $selected,
            )));

            return $selected;
        }

        $this->hideCursor();
        $this->renderMultiChoices($options, $selected, $activeIndex, $vertical);

        $sttyMode = $this->getSttyMode();
        if ($sttyMode === null) {
            fwrite(STDOUT, PHP_EOL);
            $line = $this->readFromStdin();
            if ($line !== '') {
                $selected = $this->resolveMultiChoiceIndices($line, $options, $selected);
            }

            $this->storeInput(implode(',', array_map(
                static fn (int $index): string => $options[$index],
                $selected,
            )));
            $this->showCursor();

            return $selected;
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

                if ($char === ' ') {
                    if (in_array($activeIndex, $selected, true)) {
                        $selected = array_values(array_filter(
                            $selected,
                            static fn (int $index): bool => $index !== $activeIndex,
                        ));
                    } else {
                        $selected[] = $activeIndex;
                        sort($selected);
                    }

                    $this->renderMultiChoices($options, $selected, $activeIndex, $vertical, true);

                    continue;
                }

                if ($char === "\t") {
                    $activeIndex = $this->moveChoiceIndex($activeIndex, 1, $options);
                    $this->renderMultiChoices($options, $selected, $activeIndex, $vertical, true);

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
                        $activeIndex = $this->moveChoiceIndex($activeIndex, 1, $options);
                        $this->renderMultiChoices($options, $selected, $activeIndex, $vertical, true);
                    }

                    if ($sequenceTwo === 'D' || $sequenceTwo === 'A') {
                        $activeIndex = $this->moveChoiceIndex($activeIndex, -1, $options);
                        $this->renderMultiChoices($options, $selected, $activeIndex, $vertical, true);
                    }

                    continue;
                }

                if ($char === 'h' || $char === 'k') {
                    $activeIndex = $this->moveChoiceIndex($activeIndex, -1, $options);
                    $this->renderMultiChoices($options, $selected, $activeIndex, $vertical, true);

                    continue;
                }

                if ($char === 'l' || $char === 'j') {
                    $activeIndex = $this->moveChoiceIndex($activeIndex, 1, $options);
                    $this->renderMultiChoices($options, $selected, $activeIndex, $vertical, true);

                    continue;
                }
            }
        } finally {
            $this->restoreSttyMode($sttyMode);
            $this->showCursor();
        }

        fwrite(STDOUT, PHP_EOL);
        $this->storeInput(implode(',', array_map(
            static fn (int $index): string => $options[$index],
            $selected,
        )));

        return $selected;
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
    private function renderChoices(array $options, int $selectedIndex, bool $vertical, bool $refresh = false): void
    {
        $formatted = [];

        foreach ($options as $index => $option) {
            $marker = $index === $selectedIndex ? '(*)' : '( )';
            $formatted[] = "{$marker} {$option}";
        }

        $this->renderOptionLines($formatted, $vertical, $refresh);
    }

    /**
     * @param  array<string>  $options
     * @param  array<int>  $selectedIndices
     */
    private function renderMultiChoices(array $options, array $selectedIndices, int $activeIndex, bool $vertical, bool $refresh = false): void
    {
        $formatted = [];

        foreach ($options as $index => $option) {
            $checked = in_array($index, $selectedIndices, true) ? 'x' : ' ';
            $item = sprintf('[%s] %s', $checked, $option);

            if ($index === $activeIndex) {
                $item = "\033[7m{$item}\033[0m";
            }

            $formatted[] = $item;
        }

        $this->renderOptionLines($formatted, $vertical, $refresh);
    }

    /**
     * @param  array<string>  $lines
     */
    private function renderOptionLines(array $lines, bool $vertical, bool $refresh): void
    {
        if (! $vertical) {
            fwrite(STDOUT, "\r\033[2K" . implode('   ', $lines));

            return;
        }

        $lineCount = count($lines);

        fwrite(STDOUT, "\r");
        if ($refresh && $lineCount > 1) {
            fwrite(STDOUT, "\033[" . ($lineCount - 1) . 'A');
        }

        foreach ($lines as $index => $line) {
            fwrite(STDOUT, "\033[2K{$line}");

            if ($index < $lineCount - 1) {
                fwrite(STDOUT, PHP_EOL);
            }
        }
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

    /**
     * @param  array<int>  $selectedIndices
     * @param  array<string>  $options
     * @return array<int>
     */
    private function normalizeSelectedIndices(array $selectedIndices, array $options): array
    {
        $normalized = [];

        foreach ($selectedIndices as $index) {
            if ($index < 0) {
                continue;
            }
            if ($index >= count($options)) {
                continue;
            }

            $normalized[$index] = $index;
        }

        sort($normalized);

        return $normalized;
    }

    /**
     * @param  array<string>  $options
     * @param  array<int>  $current
     * @return array<int>
     */
    private function resolveMultiChoiceIndices(string $input, array $options, array $current): array
    {
        $tokens = array_filter(array_map(trim(...), explode(',', $input)), static fn (string $token): bool => $token !== '');
        if ($tokens === []) {
            return $current;
        }

        $selected = [];

        foreach ($tokens as $token) {
            if (ctype_digit($token)) {
                $index = (int) $token - 1;
                if ($index >= 0 && $index < count($options)) {
                    $selected[$index] = $index;
                }

                continue;
            }

            foreach ($options as $index => $option) {
                if (strcasecmp($option, $token) === 0) {
                    $selected[$index] = $index;
                    break;
                }
            }
        }

        if ($selected === []) {
            return $current;
        }

        sort($selected);

        return $selected;
    }

    private function hideCursor(): void
    {
        fwrite(STDOUT, "\033[?25l");
    }

    private function showCursor(): void
    {
        fwrite(STDOUT, "\033[?25h");
    }

    private function renderCurrentNumberInput(string $input): void
    {
        fwrite(STDOUT, "\r\033[2K{$this->prompt}{$input}");
    }

    private function adjustNumericInput(
        string $input,
        int|float $step,
        int|float|null $defaultValue,
        int|float|null $minValue,
        int|float|null $maxValue,
    ): string {
        if (is_numeric($input)) {
            $value = (float) $input;

            return $this->stringifyNumber($this->clampNumber($value + $step, $minValue, $maxValue));
        }

        if ($defaultValue !== null) {
            return $this->stringifyNumber($this->clampNumber($defaultValue + $step, $minValue, $maxValue));
        }

        return $this->stringifyNumber($this->clampNumber($step, $minValue, $maxValue));
    }

    private function clampNumber(int|float $value, int|float|null $minValue, int|float|null $maxValue): int|float
    {
        if ($minValue !== null && $value < $minValue) {
            return $minValue;
        }

        if ($maxValue !== null && $value > $maxValue) {
            return $maxValue;
        }

        return $value;
    }

    private function stringifyNumber(int|float $value): string
    {
        if (is_int($value)) {
            return (string) $value;
        }

        if (fmod($value, 1.0) === 0.0) {
            return (string) (int) $value;
        }

        return rtrim(rtrim(sprintf('%.14F', $value), '0'), '.');
    }
}
