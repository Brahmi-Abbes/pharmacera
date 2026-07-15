<?php

use App\Models\Batch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Validation\ValidationException;

it('decrements the batch remaining_quantity when a sale item is created', function () {
    $batch = Batch::factory()->create(['quantity' => 50, 'remaining_quantity' => 50]);
    $sale = Sale::factory()->create();

    SaleItem::create([
        'sale_id'     => $sale->id,
        'batch_id'    => $batch->id,
        'medicine_id' => $batch->medicine_id,
        'quantity'    => 10,
        'unit_price'  => 100,
        'subtotal'    => 1000,
    ]);

    expect($batch->fresh()->remaining_quantity)->toBe(40);
});

it('refuses to sell more than the batch has remaining', function () {
    $batch = Batch::factory()->create(['quantity' => 5, 'remaining_quantity' => 5]);
    $sale = Sale::factory()->create();

    expect(fn () => SaleItem::create([
        'sale_id'     => $sale->id,
        'batch_id'    => $batch->id,
        'medicine_id' => $batch->medicine_id,
        'quantity'    => 10, // more than the 5 available
        'unit_price'  => 100,
        'subtotal'    => 1000,
    ]))->toThrow(ValidationException::class);

    // Stock must be untouched after a failed attempt.
    expect($batch->fresh()->remaining_quantity)->toBe(5);
});

it('restores stock to the old batch and deducts from the new one when a sale item is edited', function () {
    $oldBatch = Batch::factory()->create(['quantity' => 50, 'remaining_quantity' => 50]);
    $newBatch = Batch::factory()->create([
        'medicine_id'        => $oldBatch->medicine_id,
        'quantity'           => 50,
        'remaining_quantity' => 50,
    ]);
    $sale = Sale::factory()->create();

    $item = SaleItem::create([
        'sale_id'     => $sale->id,
        'batch_id'    => $oldBatch->id,
        'medicine_id' => $oldBatch->medicine_id,
        'quantity'    => 10,
        'unit_price'  => 100,
        'subtotal'    => 1000,
    ]);

    expect($oldBatch->fresh()->remaining_quantity)->toBe(40);

    // Move the sale to the new batch.
    $item->update(['batch_id' => $newBatch->id]);

    expect($oldBatch->fresh()->remaining_quantity)->toBe(50) // fully restored
        ->and($newBatch->fresh()->remaining_quantity)->toBe(40); // deducted
});

it('rolls back cleanly if the new batch does not have enough stock during an edit', function () {
    $oldBatch = Batch::factory()->create(['quantity' => 50, 'remaining_quantity' => 50]);
    $newBatch = Batch::factory()->create([
        'medicine_id'        => $oldBatch->medicine_id,
        'quantity'           => 3,
        'remaining_quantity' => 3, // not enough for the quantity below
    ]);
    $sale = Sale::factory()->create();

    $item = SaleItem::create([
        'sale_id'     => $sale->id,
        'batch_id'    => $oldBatch->id,
        'medicine_id' => $oldBatch->medicine_id,
        'quantity'    => 10,
        'unit_price'  => 100,
        'subtotal'    => 1000,
    ]);

    expect(fn () => $item->update(['batch_id' => $newBatch->id]))
        ->toThrow(ValidationException::class);

    // Old batch must be restored to exactly where it started — not left at 50
    // from the temporary rollback-undo, and not doubly deducted.
    expect($oldBatch->fresh()->remaining_quantity)->toBe(40);
});

it('returns stock to the batch when a sale item is deleted', function () {
    $batch = Batch::factory()->create(['quantity' => 50, 'remaining_quantity' => 50]);
    $sale = Sale::factory()->create();

    $item = SaleItem::create([
        'sale_id'     => $sale->id,
        'batch_id'    => $batch->id,
        'medicine_id' => $batch->medicine_id,
        'quantity'    => 10,
        'unit_price'  => 100,
        'subtotal'    => 1000,
    ]);

    expect($batch->fresh()->remaining_quantity)->toBe(40);

    $item->delete();

    expect($batch->fresh()->remaining_quantity)->toBe(50);
});
