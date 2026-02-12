<?php

declare(strict_types=1);

namespace Minicli\Components;

use Closure;
use Minicli\Contracts\InputComponentInterface;

abstract class InputComponent implements InputComponentInterface
{
    protected readonly Text $message;

    protected bool $required = true;

    /**
     * @var null|Closure(string|bool|int|float|array<mixed>): ?string
     */
    protected ?Closure $validationCallback = null;

    public function __construct(Text|string $message)
    {
        if (is_string($message)) {
            $message = Text::make($message);
        }

        $this->message = $message;
    }

    /**
     * @return string|bool|int|float|array<string>
     */
    abstract protected function readInput(): string|bool|int|float|array;

    /**
     * @return string|bool|int|float|array<string>
     */
    public function ask(): string|bool|int|float|array
    {
        while (true) {
            $this->message->render();

            $input = $this->readInput();
            if (is_string($input) && $input === '' && $this->required) {
                Alert::make('Input cannot be empty. Please provide a value.')->error()->render();
                LineBreak::make()->render();

                continue;
            }

            $validationError = $this->validateInput($input);
            if ($validationError !== null) {
                Alert::make($validationError)->error()->render();
                LineBreak::make()->render();

                continue;
            }

            return $input;
        }
    }

    /**
     * @param  callable(string|bool|int|float|array<mixed>): ?string  $callback
     */
    public function validate(callable $callback): static
    {
        $validationCallback = $callback(...);
        /** @var Closure(string|bool|int|float|array<mixed>): ?string $validationCallback */
        $this->validationCallback = $validationCallback;

        return $this;
    }

    public function required(): static
    {
        $this->required = true;

        return $this;
    }

    public function optional(): static
    {
        $this->required = false;

        return $this;
    }

    /**
     * @param  string|bool|int|float|array<mixed>  $input
     */
    protected function validateInput(string|bool|int|float|array $input): ?string
    {
        if (! $this->validationCallback instanceof Closure) {
            return null;
        }

        return ($this->validationCallback)($input);
    }
}
