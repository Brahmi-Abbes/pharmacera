<?php

use App\Support\SaleCart;

it('adds a new line item when the batch is not already in the cart', function () {
    $items = SaleCart::addOrIncrement([], medicineId: 1, batchId: 10, unitPrice: 250, maxQuantity: 20);

    expect($items)->toHaveCount(1)
        ->and($items[0])->toMatchArray([
            'medicine_id' => 1,
            'batch_id'    => 10,
            'quantity'    => 1,
            'unit_price'  => 250,
            'subtotal'    => 250,
        ]);
});

it('increments quantity instead of duplicating when the same batch is scanned again', function () {
    $items = SaleCart::addOrIncrement([], 1, 10, 250, maxQuantity: 20);
    $items = SaleCart::addOrIncrement($items, 1, 10, 250, maxQuantity: 20);

    expect($items)->toHaveCount(1)
        ->and($items[0]['quantity'])->toBe(2)
        ->and($items[0]['subtotal'])->toBe(500.0);
});

it('never increments past the batch stock ceiling', function () {
    $items = SaleCart::addOrIncrement([], 1, 10, 250, maxQuantity: 2);
    $items = SaleCart::addOrIncrement($items, 1, 10, 250, maxQuantity: 2);
    $items = SaleCart::addOrIncrement($items, 1, 10, 250, maxQuantity: 2); // third scan, only 2 in stock

    expect($items[0]['quantity'])->toBe(2);
});

it('adds a separate line item for a different batch of the same medicine', function () {
    $items = SaleCart::addOrIncrement([], 1, 10, 250, maxQuantity: 20);
    $items = SaleCart::addOrIncrement($items, 1, 11, 250, maxQuantity: 20); // different batch

    expect($items)->toHaveCount(2);
});

it('sums the total across all line items', function () {
    $items = [
        ['quantity' => 2, 'unit_price' => 100],
        ['quantity' => 3, 'unit_price' => 50],
    ];

    expect(SaleCart::total($items))->toBe(350.0);
});

it('returns zero total for an empty cart', function () {
    expect(SaleCart::total([]))->toBe(0.0);
});
