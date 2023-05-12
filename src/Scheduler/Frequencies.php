<?php

declare(strict_types=1);

namespace Minicli\Scheduler;

/**
 * @mixin Event
 */
trait Frequencies
{
    public function cron(string $expression): Event
    {
        $this->expression = $expression;

        return $this;
    }

    public function everyMinute(): Event
    {
        return $this->cron($this->expression);
    }

    public function everyTenMinutes(): Event
    {
        return $this->replaceIntoExpression(1, ['*/10']);
    }

    public function everyThirtyMinutes(): Event
    {
        return $this->replaceIntoExpression(1, ['*/30']);
    }

    public function hourlyAt(int $minute = 1): Event
    {
        return $this->replaceIntoExpression(1, [$minute]);
    }

    public function hourly(): Event
    {
        return $this->hourlyAt(1);
    }

    public function dailyAt(int $hour = 0, int $minute = 0): Event
    {
        return $this->replaceIntoExpression(1, [$minute, $hour]);
    }

    public function daily(): Event
    {
        return $this->dailyAt(0, 0);
    }

    public function twiceDaily(int $firstHour = 1, int $lastHour = 12): Event
    {
        return $this->replaceIntoExpression(1, [0, "{$firstHour},{$lastHour}"]);
    }

    public function days(): Event
    {
        return $this->replaceIntoExpression(5, (array) implode(',', func_get_args() ?: ['*']));
    }

    public function mondays(): Event
    {
        return $this->days(1);
    }

    public function tuesdays(): Event
    {
        return $this->days(2);
    }

    public function wednesdays(): Event
    {
        return $this->days(3);
    }

    public function thursdays(): Event
    {
        return $this->days(4);
    }

    public function fridays(): Event
    {
        return $this->days(5);
    }

    public function saturdays(): Event
    {
        return $this->days(6);
    }

    public function sundays(): Event
    {
        return $this->days(7);
    }

    public function weekdays(): Event
    {
        return $this->days(1, 2, 3, 4, 5);
    }

    public function weekends(): Event
    {
        return $this->days(6, 7);
    }

    public function at(int $hour = 0, int $minute = 0): Event
    {
        return $this->dailyAt($hour, $minute);
    }

    public function monthly(): Event
    {
        return $this->monthlyOn(1);
    }

    public function monthlyOn(int $day = 1): Event
    {
        return $this->replaceIntoExpression(1, [0, 0, $day]);
    }

    /**
     * @param int $position
     * @param array<int,int|string> $value
     * @return Event
     */
    public function replaceIntoExpression(int $position, array $value): Event
    {
        $expression = explode(' ', $this->expression);

        array_splice($expression, $position - 1, 1, $value);

        $expression = array_slice($expression, 0, 5);

        return $this->cron(implode(' ', $expression));
    }
}