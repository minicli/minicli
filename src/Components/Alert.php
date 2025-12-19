<?php

declare(strict_types=1);

namespace Minicli\Components;

use Minicli\Output\Theming\StyleType;

class Alert extends Component
{
    private StyleType $style = StyleType::DEFAULT;

    public function __construct(
        private string $message,
        private string $title = '',
    ) {}

    public static function make(string $message, string $title = ''): self
    {
        return new self($message, $title);
    }

    public function default(): self
    {
        $this->style = StyleType::DEFAULT;

        return $this;
    }

    public function error(): self
    {
        $this->style = StyleType::ERROR;

        return $this;
    }

    public function warning(): self
    {
        $this->style = StyleType::WARNING;

        return $this;
    }

    public function success(): self
    {
        $this->style = StyleType::SUCCESS;

        return $this;
    }

    public function info(): self
    {
        $this->style = StyleType::INFO;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function output(): string
    {
        $styles = [$this->style, StyleType::INVERT];
        $titleStyles = [...$styles, StyleType::BOLD];

        // Split message into lines
        $messageLines = explode("\n", $this->message);

        // Calculate the maximum width needed across all lines
        $titleLength = mb_strlen($this->title);
        $maxLength = $titleLength;
        foreach ($messageLines as $line) {
            $maxLength = max($maxLength, mb_strlen($line));
        }

        // Add padding on both sides (2 spaces on each side)
        $paddingSize = 2;
        $boxWidth = $maxLength + ($paddingSize * 2);

        $content = "\n";

        // Add top padding line
        $content .= $this->printer()->out($this->filter()->filter(paddedString('', $boxWidth), $styles)) . "\n";

        if ($this->title !== '') {
            $paddedTitle = paddedString("  {$this->title}", $boxWidth);
            $content .= "{$this->printer()->out($this->filter()->filter($paddedTitle, $titleStyles))}\n";
        }

        // Add each message line with padding
        foreach ($messageLines as $line) {
            $paddedLine = paddedString("  {$line}", $boxWidth);
            $content .= $this->printer()->out($this->filter()->filter($paddedLine, $styles)) . "\n";
        }

        // Add bottom padding line
        $content .= $this->printer()->out($this->filter()->filter(paddedString('', $boxWidth), $styles)) . "\n";

        return $content;
    }
}
