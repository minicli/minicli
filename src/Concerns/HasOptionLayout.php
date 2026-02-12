<?php

declare(strict_types=1);

namespace Minicli\Concerns;

trait HasOptionLayout
{
    private bool $verticalLayout = false;

    public function horizontal(): static
    {
        $this->verticalLayout = false;

        return $this;
    }

    public function vertical(): static
    {
        $this->verticalLayout = true;

        return $this;
    }

    protected function isVerticalLayout(): bool
    {
        return $this->verticalLayout;
    }
}
