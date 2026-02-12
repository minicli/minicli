<?php

declare(strict_types=1);

namespace Minicli\Components;

use InvalidArgumentException;
use Minicli\Concerns\HasOptionLayout;
use Minicli\Input\Input;

final class Select extends InputComponent
{
    use HasOptionLayout;

    /**
     * @var array<string>
     */
    private array $labels = [];

    /**
     * @var array<string>
     */
    private array $values = [];

    private ?string $defaultValue = null;

    public static function make(Text|string $message): self
    {
        return new self($message);
    }

    /**
     * @param  array<string, string>|list<string>  $options
     */
    public function options(array $options): self
    {
        $this->labels = [];
        $this->values = [];

        if (array_is_list($options)) {
            foreach ($options as $option) {
                $label = (string) $option;
                $this->labels[] = $label;
                $this->values[] = $label;
            }

            return $this;
        }

        foreach ($options as $value => $label) {
            $this->labels[] = (string) $label;
            $this->values[] = (string) $value;
        }

        return $this;
    }

    public function default(string $value): self
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function ask(): string
    {
        $selected = parent::ask();

        return is_string($selected) ? $selected : '';
    }

    protected function readInput(): string
    {
        if ($this->labels === []) {
            throw new InvalidArgumentException('Select options cannot be empty.');
        }

        $defaultIndex = 0;

        if ($this->defaultValue !== null) {
            $index = array_search($this->defaultValue, $this->values, true);
            if (is_int($index)) {
                $defaultIndex = $index;
            }
        }

        $selectedIndex = new Input('')->readChoice(
            options: $this->labels,
            selectedIndex: $defaultIndex,
            vertical: $this->isVerticalLayout(),
        );

        return $this->values[$selectedIndex];
    }
}
