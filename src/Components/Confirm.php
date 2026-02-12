<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Concerns\HasOptionLayout;
use Minicli\Input\Input;

final class Confirm extends InputComponent
{
    use HasOptionLayout;

    private string $yesLabel = 'Yes';

    private string $noLabel = 'No';

    private bool $defaultValue = true;

    public static function make(Text|string $message): self
    {
        return new self($message);
    }

    public function yes(string $label): self
    {
        $this->yesLabel = $label;

        return $this;
    }

    public function no(string $label): self
    {
        $this->noLabel = $label;

        return $this;
    }

    public function default(bool $value): self
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function ask(): bool
    {
        $selected = parent::ask();

        return is_bool($selected) && $selected;
    }

    protected function readInput(): bool
    {
        $selected = new Input('')->readChoice(
            options: [$this->yesLabel, $this->noLabel],
            selectedIndex: $this->defaultValue ? 0 : 1,
            vertical: $this->isVerticalLayout(),
        );

        return $selected === 0;
    }
}
