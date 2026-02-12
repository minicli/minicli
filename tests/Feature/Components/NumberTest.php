<?php

declare(strict_types=1);

it('uses default number in non-interactive mode when input is empty', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Number::make('How many?')->default(7)->ask();
echo "RESULT={$value}";
PHP, "\n");

    expect($output)->toContain('RESULT=7');
});
