<?php

declare(strict_types=1);

it('resolves multi-select values from mixed tokens', function (): void {
    $output = runInlinePhpWithInput(<<<'PHP'
$value = Minicli\Components\MultiSelect::make('Pick many')
    ->options(['first' => 'First', 'second' => 'Second'])
    ->ask();
echo 'RESULT=' . json_encode($value);
PHP, "1,Second\n");

    expect($output)->toContain('RESULT=["first","second"]');
});
