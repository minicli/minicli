<?php

declare(strict_types=1);

namespace Minicli\Components;

use InvalidArgumentException;
use Minicli\Concerns\HasOptionLayout;
use Minicli\Input\Input;

final class MultiSelect extends InputComponent
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

    /**
     * @var array<string>
     */
    private array $defaultValues = [];

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

    /**
     * @param  array<string>  $values
     */
    public function default(array $values): self
    {
        $this->defaultValues = $values;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function ask(): array
    {
        $selected = parent::ask();

        return is_array($selected) ? $selected : [];
    }

    /**
     * @return array<string>
     */
    protected function readInput(): array
    {
        if ($this->labels === []) {
            throw new InvalidArgumentException('MultiSelect options cannot be empty.');
        }

        $defaultIndices = [];

        foreach ($this->defaultValues as $value) {
            $index = array_search($value, $this->values, true);
            if (is_int($index)) {
                $defaultIndices[] = $index;
            }
        }

        $selectedIndices = new Input('')->readMultiChoice(
            options: $this->labels,
            selectedIndices: $defaultIndices,
            vertical: $this->isVerticalLayout(),
        );

        return array_values(array_map(
            fn (int $index): string => $this->values[$index],
            $selectedIndices,
        ));
    }
}
