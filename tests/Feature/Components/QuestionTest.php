<?php

declare(strict_types=1);

it('reads question input in non-interactive mode', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Question::make('Name?')->ask();
echo "RESULT={$value}";
PHP, "Erika\n");

    expect($output)->toContain('RESULT=Erika');
});
