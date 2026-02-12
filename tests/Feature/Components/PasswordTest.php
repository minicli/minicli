<?php

declare(strict_types=1);

it('reads hidden password input in non-interactive mode', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Password::make('Password?')->ask();
echo "RESULT={$value}";
PHP, "supersecret\n");

    expect($output)->toContain('RESULT=supersecret');
});
