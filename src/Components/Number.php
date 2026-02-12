<?php

declare(strict_types=1);

namespace Minicli\Components;

use InvalidArgumentException;
use Minicli\Input\Input;

final class Number extends InputComponent
{
    private int|float|null $defaultValue = null;

    private int|float $step = 1;

    private int|float|null $minValue = null;

    private int|float|null $maxValue = null;

    public static function make(Text|string $message): self
    {
        return new self($message);
    }

    public function default(int|float $value): self
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function step(int|float $step): self
    {
        if ($step <= 0) {
            throw new InvalidArgumentException('Step must be greater than zero.');
        }

        $this->step = $step;

        return $this;
    }

    public function min(int|float $value): self
    {
        if ($this->maxValue !== null && $value > $this->maxValue) {
            throw new InvalidArgumentException('Min value cannot be greater than max value.');
        }

        $this->minValue = $value;

        return $this;
    }

    public function max(int|float $value): self
    {
        if ($this->minValue !== null && $value < $this->minValue) {
            throw new InvalidArgumentException('Max value cannot be less than min value.');
        }

        $this->maxValue = $value;

        return $this;
    }

    public function ask(): int|float
    {
        while (true) {
            $this->message->render();

            $selected = $this->readInput();
            if (is_string($selected) && $selected === '') {
                continue;
            }

            if (is_int($selected) || is_float($selected)) {
                $validationError = $this->validateInput($selected);
                if ($validationError !== null) {
                    Alert::make($validationError)->error()->render();
                    LineBreak::make()->render();

                    continue;
                }

                return $selected;
            }

            return 0;
        }
    }

    protected function readInput(): string|int|float
    {
        $input = new Input()->readNumber(
            defaultValue: $this->defaultValue,
            step: $this->step,
            minValue: $this->minValue,
            maxValue: $this->maxValue,
        );

        if ($input === '') {
            if ($this->defaultValue !== null) {
                return $this->clampToBounds($this->defaultValue);
            }

            if (! $this->required) {
                return $this->clampToBounds(0);
            }

            Alert::make('Input cannot be empty. Please provide a value.')->error()->render();
            LineBreak::make()->render();

            return '';
        }

        if (! is_numeric($input)) {
            Alert::make('Input must be a valid number.')->error()->render();
            LineBreak::make()->render();

            return '';
        }

        $value = $this->toNumeric($input);

        if ($this->isBelowMin($value) || $this->isAboveMax($value)) {
            Alert::make($this->rangeErrorMessage())->error()->render();
            LineBreak::make()->render();

            return '';
        }

        return $value;
    }

    private function toNumeric(string $value): int|float
    {
        if (str_contains($value, '.') || str_contains($value, 'e') || str_contains($value, 'E')) {
            return (float) $value;
        }

        return (int) $value;
    }

    private function clampToBounds(int|float $value): int|float
    {
        if ($this->minValue !== null && $value < $this->minValue) {
            return $this->minValue;
        }

        if ($this->maxValue !== null && $value > $this->maxValue) {
            return $this->maxValue;
        }

        return $value;
    }

    private function isBelowMin(int|float $value): bool
    {
        return $this->minValue !== null && $value < $this->minValue;
    }

    private function isAboveMax(int|float $value): bool
    {
        return $this->maxValue !== null && $value > $this->maxValue;
    }

    private function rangeErrorMessage(): string
    {
        if ($this->minValue !== null && $this->maxValue !== null) {
            return sprintf('Input must be between %s and %s.', (string) $this->minValue, (string) $this->maxValue);
        }

        if ($this->minValue !== null) {
            return sprintf('Input must be greater than or equal to %s.', (string) $this->minValue);
        }

        if ($this->maxValue !== null) {
            return sprintf('Input must be less than or equal to %s.', (string) $this->maxValue);
        }

        return 'Input is out of the allowed range.';
    }
}
