<?php

declare(strict_types=1);

it('uses default number in non-interactive mode when input is empty', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Number::make('How many?')->default(7)->ask();
echo "RESULT={$value}";
PHP, "\n");

    expect($output)->toContain('RESULT=7');
});

it('retries number input when validation fails', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Number::make('How many?')
    ->validate(fn (int|float $input): ?string => $input < 18 ? 'Input must be at least 18.' : null)
    ->ask();
echo "RESULT={$value}";
PHP, "16\n18\n");

    expect($output)
        ->toContain('Input must be at least 18.')
        ->toContain('RESULT=18');
});
