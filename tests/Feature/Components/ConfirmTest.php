<?php

declare(strict_types=1);

it('resolves confirm answers from typed choice labels', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Confirm::make('Continue?')->yes('Yep')->no('Nope')->default(true)->ask();
echo $value ? 'RESULT=true' : 'RESULT=false';
PHP, "Nope\n");

    expect($output)->toContain('RESULT=false');
});

it('retries confirm input when validation fails', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Confirm::make('Continue?')
    ->yes('Yes')
    ->no('No')
    ->default(true)
    ->validate(fn (bool $input): ?string => $input ? null : 'You must accept to continue.')
    ->ask();
echo $value ? 'RESULT=true' : 'RESULT=false';
PHP, "No\nYes\n");

    expect($output)
        ->toContain('You must accept to continue.')
        ->toContain('RESULT=true');
});
