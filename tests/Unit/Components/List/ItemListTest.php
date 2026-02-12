<?php

declare(strict_types=1);

use Minicli\Components\List\Item;
use Minicli\Components\List\ItemList;

it('renders item lists with descriptions', function (): void {
    $list = ItemList::make();
    $list->addItem(Item::make('help', 'show help'));
    $list->addItem(Item::make('test', 'run tests'));

    $output = $list->output();

    expect($list->totalItems())->toBe(2)
        ->and($output)->toContain('help')
        ->and($output)->toContain('show help');
});
