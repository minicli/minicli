<?php

declare(strict_types=1);

it('resolves single select option from typed label to value', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Select::make('Pick one')
    ->options(['first' => 'First', 'second' => 'Second'])
    ->default('first')
    ->ask();
echo "RESULT={$value}";
PHP, "Second\n");

    expect($output)->toContain('RESULT=second');
});

it('retries select input when validation fails', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Select::make('Pick one')
    ->options(['first' => 'First', 'second' => 'Second'])
    ->default('first')
    ->validate(fn (string $input): ?string => $input === 'first' ? 'Please pick a non-default option.' : null)
    ->ask();
echo "RESULT={$value}";
PHP, "First\nSecond\n");

    expect($output)
        ->toContain('Please pick a non-default option.')
        ->toContain('RESULT=second');
});
