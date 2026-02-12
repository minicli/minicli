<?php

declare(strict_types=1);

it('resolves confirm answers from typed choice labels', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\Confirm::make('Continue?')->yes('Yep')->no('Nope')->default(true)->ask();
echo $value ? 'RESULT=true' : 'RESULT=false';
PHP, "Nope\n");

    expect($output)->toContain('RESULT=false');
});
