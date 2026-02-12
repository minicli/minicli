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
