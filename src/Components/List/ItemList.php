<?php

declare(strict_types=1);

namespace Minicli\Components\List;

use Minicli\Components\Component;
use Minicli\Components\Text;

class ItemList extends Component
{
    /** @var array<Item> */
    protected array $items;

    protected string $separator = '.';

    protected bool $fullWidth = false;

    protected int $indentOffset = 0;

    protected int $parentMaxWidth = 0;

    /**
     * @param  array<Item>|null  $items
     */
    public function __construct(?array $items = null)
    {
        if (is_array($items)) {
            $this->setItems($items);
        }
    }

    /**
     * @param  array<Item>|null  $items
     */
    public static function make(?array $items = null): self
    {
        return new self($items);
    }

    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    public function separator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    public function fullWidth(): self
    {
        $this->fullWidth = true;

        return $this;
    }

    public function compact(): self
    {
        $this->fullWidth = false;

        return $this;
    }

    public function totalItems(): int
    {
        return count($this->items);
    }

    public function output(): string
    {
        $maxNameWidth = $this->calculateMaxNameWidth();

        // For nested lists in compact mode, ensure alignment is 10 chars after parent
        if ($this->parentMaxWidth > 0 && ! $this->fullWidth) {
            // Parent descriptions start at: parentMaxWidth + 10
            // Nested descriptions should start at: parentMaxWidth + 20 (10 more)
            $minNestedAlignment = $this->parentMaxWidth + 20;
            // Use the larger of nested items' own max width or the target alignment
            $maxNameWidth = max($maxNameWidth, $minNestedAlignment);
        }

        $output = '';

        foreach ($this->items as $item) {
            $item->applyStylesToFields();
            $item->name->withoutLineBreak();

            if ($item->hasDescription()) {
                /** @var Text $description */
                $description = $item->description;
                $description->withoutLineBreak();

                $nameOutput = $item->name->output();
                $descriptionOutput = $description->output();

                if ($this->fullWidth) {
                    $terminalWidth = $this->terminalWidth() - $this->indentOffset;
                    $nameVisualWidth = $this->visualWidth($nameOutput);
                    $descriptionVisualWidth = $this->visualWidth($descriptionOutput);
                    $separatorCount = $terminalWidth - $nameVisualWidth - $descriptionVisualWidth;
                    $separatorCount = max($separatorCount, 1);
                } else {
                    $nameContent = $item->name->content();
                    $nameWidth = $this->stringWidth($nameContent);
                    $separatorCount = ($maxNameWidth - $nameWidth) + 10;
                }

                $output .= $nameOutput;
                $output .= str_repeat($this->separator, $separatorCount);
                $output .= $descriptionOutput;
                $output .= "\n";
            } else {
                $output .= $item->name->output();
                $output .= "\n";
            }

            if ($item->hasNested()) {
                /** @var ItemList $nestedList */
                $nestedList = $item->nested;

                // Set indent offset for nested list (tab width = 8)
                if ($this->fullWidth) {
                    $nestedList->indentOffset = $this->indentOffset + 8;
                } else {
                    // In compact mode, pass max width to nested list
                    $nestedList->parentMaxWidth = $maxNameWidth;
                }

                $nestedOutput = $nestedList->output();
                $output .= preg_replace('/^/m', "\t", $nestedOutput);
            }
        }

        return $output;
    }

    /**
     * @param  array<Item>  $items
     */
    protected function setItems(array $items): void
    {
        foreach ($items as $item) {
            $this->addItem($item);
        }
    }

    protected function calculateMaxNameWidth(): int
    {
        $names = array_map(
            fn (Item $item): string => $item->name->content(),
            array_filter($this->items, fn (Item $item): bool => $item->hasDescription())
        );

        return $names !== [] ? $this->maxStringWidth($names) : 0;
    }
}
