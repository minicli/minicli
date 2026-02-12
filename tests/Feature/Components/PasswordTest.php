<?php

declare(strict_types=1);

it('reads hidden password input in non-interactive mode', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Password::make('Password?')->ask();
echo "RESULT={$value}";
PHP, "supersecret\n");

    expect($output)->toContain('RESULT=supersecret');
});

it('retries password input when validation fails', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Password::make('Password?')
    ->validate(fn (string $input): ?string => strlen($input) < 8 ? 'Password must have at least 8 characters.' : null)
    ->ask();
echo "RESULT={$value}";
PHP, "short\nsupersecret\n");

    expect($output)
        ->toContain('Password must have at least 8 characters.')
        ->toContain('RESULT=supersecret');
});
