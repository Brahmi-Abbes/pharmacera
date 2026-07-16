<?php

use App\Models\Batch;
use App\Models\Sale;
use App\Models\SaleItem;
use Spatie\Activitylog\Models\Activity;

it('logs the batch stock change to the activity log when a sale item is created', function () {
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

    $activity = Activity::query()
        ->where('subject_type', Batch::class)
        ->where('subject_id', $batch->id)
        ->where('log_name', 'batch')
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->description)->toBe('updated');

    $changes = $activity->attribute_changes;

    expect($changes['old']['remaining_quantity'])->toBe(50)
        ->and($changes['attributes']['remaining_quantity'])->toBe(40);
});

it('logs the batch stock restoration to the activity log when a sale item is deleted', function () {
    $batch = Batch::factory()->create(['quantity' => 50, 'remaining_quantity' => 50]);
    $sale = Sale::factory()->create();

    $saleItem = SaleItem::create([
        'sale_id'     => $sale->id,
        'batch_id'    => $batch->id,
        'medicine_id' => $batch->medicine_id,
        'quantity'    => 10,
        'unit_price'  => 100,
        'subtotal'    => 1000,
    ]);

    $saleItem->delete();

    $activity = Activity::query()
        ->where('subject_type', Batch::class)
        ->where('subject_id', $batch->id)
        ->where('log_name', 'batch')
        ->latest('id')
        ->first();

    $changes = $activity->attribute_changes;

    expect($changes['old']['remaining_quantity'])->toBe(40)
        ->and($changes['attributes']['remaining_quantity'])->toBe(50);
});
