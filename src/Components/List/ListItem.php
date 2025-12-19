<?php

declare(strict_types=1);

namespace Minicli\Components\List;

use Minicli\Components\Text;
use Minicli\Concerns\HasStyles;

final class ListItem
{
    use HasStyles;

    public Text $name;

    public ?Text $description = null;

    public ?ItemList $nested = null;

    public function __construct(
        Text|string $name,
        Text|string|null $description = null,
    ) {
        $this->name = $name instanceof Text ? $name : Text::make($name);

        if ($description !== null) {
            $this->description = $description instanceof Text ? $description : Text::make($description);
        }
    }

    public static function make(Text|string $name, Text|string|null $description = null): self
    {
        return new self($name, $description);
    }

    public function nested(ItemList $nested): self
    {
        $this->nested = $nested;

        return $this;
    }

    public function hasNested(): bool
    {
        return $this->nested instanceof ItemList;
    }

    public function hasDescription(): bool
    {
        return $this->description instanceof Text;
    }

    public function applyStylesToFields(): void
    {
        if (! $this->hasStyles()) {
            return;
        }

        if (! $this->name->hasStyles()) {
            $this->name->applyStyles($this->styles());

            if ($this->isAlt()) {
                $this->name->alt();
            }
        }

        if ($this->description instanceof Text && ! $this->description->hasStyles()) {
            $this->description->applyStyles($this->styles());

            if ($this->isAlt()) {
                $this->description->alt();
            }
        }
    }
}
