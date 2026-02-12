<?php

declare(strict_types=1);

namespace Minicli\Components;

final class ProgressBar extends Component
{
    private Text $message;

    private float $progress = 0.0;

    private bool $finished = false;

    /**
     * @var list<mixed>
     */
    private array $steps = [];

    /**
     * @var callable(mixed, int): void|null
     */
    private $callback;

    private int $width = 30;

    public function __construct(Text|string $message)
    {
        if (is_string($message)) {
            $message = Text::make($message);
        }

        $this->message = $message;
    }

    public static function make(Text|string $message): self
    {
        return new self($message);
    }

    public function text(Text|string $message): self
    {
        if (is_string($message)) {
            $message = Text::make($message);
        }

        $this->message = $message;

        return $this;
    }

    /**
     * @param  list<mixed>  $steps
     */
    public function steps(array $steps): self
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * @param  callable(mixed, int): void  $callback
     */
    public function callback(callable $callback): self
    {
        $this->callback = $callback;

        return $this;
    }

    public function setProgress(int|float $progress): self
    {
        if (self::$quiet) {
            return $this;
        }

        if ($this->finished) {
            return $this;
        }

        $this->progress = max(0.0, min(100.0, (float) $progress));
        $this->renderProgress();

        if ($this->progress >= 100.0) {
            $this->finish();
        }

        return $this;
    }

    public function run(): self
    {
        if ($this->steps === []) {
            return $this->setProgress(100);
        }

        $total = count($this->steps);
        $this->setProgress(0);

        foreach ($this->steps as $index => $step) {
            if (is_callable($this->callback)) {
                ($this->callback)($step, $index);
            }

            $this->setProgress((($index + 1) / $total) * 100);
        }

        return $this;
    }

    public function finish(): self
    {
        if (self::$quiet) {
            return $this;
        }

        if ($this->finished) {
            return $this;
        }

        if ($this->progress < 100.0) {
            $this->progress = 100.0;
            $this->renderProgress();
        }

        if ($this->isInteractiveOutput()) {
            fwrite(STDOUT, PHP_EOL);
        }

        $this->finished = true;

        return $this;
    }

    public function output(): string
    {
        return $this->formattedLine() . ($this->isInteractiveOutput() ? '' : PHP_EOL);
    }

    private function renderProgress(): void
    {
        $line = $this->formattedLine();

        if ($this->isInteractiveOutput()) {
            fwrite(STDOUT, "\r\033[2K{$line}");

            return;
        }

        fwrite(STDOUT, $line . PHP_EOL);
    }

    private function formattedLine(): string
    {
        $filled = (int) round(($this->progress / 100) * $this->width);
        $empty = $this->width - $filled;
        $bar = str_repeat('=', $filled) . str_repeat('-', $empty);
        $percent = sprintf('%3d', (int) round($this->progress));
        $text = $this->message->withoutLineBreak()->output();

        return sprintf('%s [%s] %s%%', $text, $bar, $percent);
    }

    private function isInteractiveOutput(): bool
    {
        return defined('STDOUT') && stream_isatty(STDOUT);
    }
}
