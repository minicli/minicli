<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Contracts\InputComponentInterface;

abstract class InputComponent implements InputComponentInterface
{
    protected readonly Text $message;

    protected bool $required = true;

    public function __construct(Text|string $message)
    {
        if (is_string($message)) {
            $message = Text::make($message);
        }

        $this->message = $message;
    }

    abstract protected function readInput(): string;

    public function ask(): string
    {
        $this->message->render();

        $input = $this->readInput();
        if ($input === '' && $this->required) {
            Alert::make('Input cannot be empty. Please provide a value.')->warning()->render();
            LineBreak::make()->render();

            return $this->ask();
        }

        return $input;
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
}
