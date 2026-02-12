<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Input\Input;

class Question extends InputComponent
{
    public static function make(Text|string $message): self
    {
        return new self($message);
    }

    protected function readInput(): string
    {
        return new Input()->read();
    }
}
