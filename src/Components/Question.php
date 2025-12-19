<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Input\Input;

class Question
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

        $input = new Input()->read();
        if ($input === '' && $this->required) {
            Text::make('Input cannot be empty. Please provide a value.')->warning()->render();
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
}
