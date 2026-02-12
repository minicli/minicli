<?php

declare(strict_types=1);

it('reads question input in non-interactive mode', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Question::make('Name?')->ask();
echo "RESULT={$value}";
PHP, "Erika\n");

    expect($output)->toContain('RESULT=Erika');
});

it('retries question input when validation fails', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Question::make('Name?')
    ->validate(fn (string $input): ?string => strlen($input) < 3 ? 'Name must have at least 3 characters.' : null)
    ->ask();
echo "RESULT={$value}";
PHP, "Al\nAlex\n");

    expect($output)
        ->toContain('Name must have at least 3 characters.')
        ->toContain('RESULT=Alex');
});
