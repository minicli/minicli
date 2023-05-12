<?php

declare(strict_types=1);

namespace Minicli\Scheduler;

use DateTimeInterface;

abstract class Event
{
    use Frequencies;

    public string $expression = '* * * * *';

    abstract public function handle(): void;

    /**
     * @param DateTimeInterface|null $date
     * @return bool
     * @todo Check to see if this event is due to be ran. Look at cron-expression package for inspiration.
     */
    public function isDue(null|DateTimeInterface $date = null): bool
    {
        if (! $date) {
            return false;
        }

        return true;
    }
}