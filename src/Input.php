<?php

declare(strict_types=1);

namespace Minicli;

use RuntimeException;

class Input
{
    /**
     * input history
     *
     * @var array
     */
    protected array $inputHistory = [];

    /**
     * prompt
     *
     * @var string
     */
    protected string $prompt;

    /**
     * constructor
     *
     * @param string $prompt
     */
    public function __construct($prompt = 'minicli$> ')
    {
        $this->checkReadlineExtension();
        $this->setPrompt($prompt);
    }

    /**
     * read input
     *
     * @return string
     */
    public function read(): string
    {
        $this->checkReadlineExtension();
        $input = readline($this->getPrompt());
        $this->inputHistory[] = $input;

        return $input;
    }

    /**
     * get input history
     *
     * @return array
     */
    public function getInputHistory(): array
    {
        return $this->inputHistory;
    }

    /**
     * get prompt
     *
     * @return string
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * set prompt
     *
     * @param string $prompt
     * @return void
     */
    public function setPrompt(string $prompt): void
    {
        $this->prompt = $prompt;
    }

    /**
     * check read line extension
     *
     * @return void
     * @throws RuntimeException
     */
    private function checkReadlineExtension(): void
    {
        if (extension_loaded('readline')) {
            return;
        }

        throw new RuntimeException('Extension readline is required.');
    }
}
