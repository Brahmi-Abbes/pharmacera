<?php

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\User;
use App\Notifications\LowStockAlert;
use App\Notifications\OutOfStockAlert;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'pharmacist', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'cashier', 'guard_name' => 'web']);
});

it('sends a low stock alert only when crossing the threshold, and only to admin/pharmacist', function () {
    Notification::fake();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $cashier = User::factory()->create();
    $cashier->assignRole('cashier');

    $medicine = Medicine::factory()->create(['alert_threshold' => 10]);
    $batch = Batch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity' => 20,
        'remaining_quantity' => 20,
    ]);

    // Still above threshold (20 -> 15) — no alert expected.
    $batch->update(['remaining_quantity' => 15]);
    Notification::assertNothingSent();

    // Crosses the threshold (15 -> 8) — alert expected, admin only.
    $batch->update(['remaining_quantity' => 8]);

    Notification::assertSentTo($admin, LowStockAlert::class);
    Notification::assertNotSentTo($cashier, LowStockAlert::class);

    // Already below threshold (8 -> 5) — no second alert for the same crossing.
    Notification::fake();
    $batch->update(['remaining_quantity' => 5]);
    Notification::assertNothingSent();
});

it('sends an out of stock alert when stock hits zero, not a low stock alert', function () {
    Notification::fake();

    $pharmacist = User::factory()->create();
    $pharmacist->assignRole('pharmacist');

    $medicine = Medicine::factory()->create(['alert_threshold' => 10]);
    $batch = Batch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity' => 5,
        'remaining_quantity' => 5,
    ]);

    $batch->update(['remaining_quantity' => 0]);

    Notification::assertSentTo($pharmacist, OutOfStockAlert::class);
    Notification::assertNotSentTo($pharmacist, LowStockAlert::class);
});

it('does not alert when stock changes but stays above the threshold', function () {
    Notification::fake();

    User::factory()->create()->assignRole('admin');

    $medicine = Medicine::factory()->create(['alert_threshold' => 10]);
    $batch = Batch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity' => 100,
        'remaining_quantity' => 100,
    ]);

    $batch->update(['remaining_quantity' => 50]);

    Notification::assertNothingSent();
});
