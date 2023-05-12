<?php

declare(strict_types=1);

namespace Minicli\Scheduler;

use DateTimeImmutable;
use DateTimeInterface;

final class Kernel
{
    /**
     * @param array<int,Event> $events
     * @param DateTimeInterface|null $date
     */
    public function __construct(
        protected array $events = [],
        protected null|DateTimeInterface $date = null,
    ) {
    }

    /**
     * @return array<int,Event>
     */
    public function events(): array
    {
        return $this->events;
    }

    public function date(): DateTimeInterface
    {
        if (! $this->date) {
            return new DateTimeImmutable();
        }

        return $this->date;
    }

    public function add(Event $event): Event
    {
        $this->events[] = $event;

        return $event;
    }

    public function run(): void
    {
        foreach ($this->events() as $event) {
            if (! $event->isDue($this->date)) {
                continue;
            }

            $event->handle();
        }
    }
}
