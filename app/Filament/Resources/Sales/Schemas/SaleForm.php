<?php

namespace App\Filament\Resources\Sales\Schemas;

use App\Models\Batch;
use App\Models\Medicine;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use App\Support\SaleCart;
use Filament\Notifications\Notification;


class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('pharmacy.sale.cashier'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('payment_method')
                    ->label(__('pharmacy.sale.payment_method'))
                    ->options([
                        'cash' => __('pharmacy.sale.cash'),
                        'card' => __('pharmacy.sale.card'),
                        'insurance' => __('pharmacy.sale.insurance'),
                    ])
                    ->default('cash')
                    ->required(),
                TextInput::make('barcode_scan')
                    ->label(__('pharmacy.sale.barcode_scan'))
                    ->placeholder(__('pharmacy.sale.barcode_scan_placeholder'))
                    ->live(onBlur: true)
                    ->extraInputAttributes([
                        'autofocus' => true,
                        'x-on:keydown.enter.prevent' => '$el.blur()',
                    ])
                    ->dehydrated(false)
                    ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                        if (blank($state)) {
                            return;
                        }

                        $medicine = Medicine::findByBarcode($state);

                        if (! $medicine) {
                            Notification::make()->title(__('pharmacy.sale.no_medicine_for_barcode'))->danger()->send();
                            return $set('barcode_scan', null);
                        }

                        $batch = $medicine->nextAvailableBatch();

                        if (! $batch) {
                            Notification::make()->title(__('pharmacy.sale.no_stock_for_medicine', ['medicine' => $medicine->name]))->danger()->send();
                            return $set('barcode_scan', null);
                        }

                        $items = SaleCart::addOrIncrement($get('items') ?? [], $medicine->id, $batch->id, $medicine->selling_price, $batch->remaining_quantity);

                        $set('items', $items);
                        $set('total', SaleCart::total($items));
                        $set('barcode_scan', null);
                    })
                    ->columnSpanFull(),
                Repeater::make('items')
                    ->label(__('pharmacy.sale.items'))
                    ->relationship()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $total = collect($get('items'))
                            ->sum(fn ($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0));

                        $set('total', round($total, 2));
                    })
                    ->schema([
                        Select::make('medicine_id')
                            ->label(__('pharmacy.sale.medicine'))
                            ->relationship('medicine', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('batch_id', null);
                                $set('unit_price', $state ? Medicine::find($state)?->selling_price : null);
                            }),
                        Select::make('batch_id')
                            ->label(__('pharmacy.sale.batch'))
                            ->options(function (Get $get): array {
                                $medicineId = $get('medicine_id');

                                if (blank($medicineId)) {
                                    return [];
                                }

                                return Batch::query()
                                    ->where('medicine_id', $medicineId)
                                    ->where('remaining_quantity', '>', 0)
                                    ->orderBy('expiry_date')
                                    ->get()
                                    ->mapWithKeys(fn (Batch $batch) => [
                                        $batch->id => "#{$batch->id} · exp {$batch->expiry_date->format('Y-m-d')} · {$batch->remaining_quantity} left",
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            // Reset quantity when the batch changes so a stale
                            // quantity from a previous batch can't slip through.
                            ->afterStateUpdated(fn (Set $set) => $set('quantity', 1)),
                        TextInput::make('quantity')
                            ->label(__('pharmacy.sale.quantity'))
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            // Hard-caps the input at what's actually left in the
                            // chosen batch — the SaleItemObserver enforces this
                            // again server-side as a safety net.
                            ->maxValue(function (Get $get): ?int {
                                $batchId = $get('batch_id');

                                return blank($batchId) ? null : Batch::find($batchId)?->remaining_quantity;
                            })
                            ->helperText(function (Get $get): ?string {
                                $batchId = $get('batch_id');

                                if (blank($batchId)) {
                                    return null;
                                }

                                $remaining = Batch::find($batchId)?->remaining_quantity;

                                return $remaining === null ? null : __('pharmacy.sale.available_in_batch', ['count' => $remaining]);
                            })
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('subtotal', round((float) $get('quantity') * (float) $get('unit_price'), 2));
                            }),
                        TextInput::make('unit_price')
                            ->label(__('pharmacy.sale.unit_price'))
                            ->required()
                            ->numeric()
                            ->suffix(fn () => \App\Models\Setting::currency())
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('subtotal', round((float) $get('quantity') * (float) $get('unit_price'), 2));
                            }),
                        TextInput::make('subtotal')
                            ->label(__('pharmacy.sale.subtotal'))
                            ->required()
                            ->numeric()
                            ->suffix(fn () => \App\Models\Setting::currency())
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->minItems(1)
                    ->required(),
                TextInput::make('total')
                    ->label(__('pharmacy.sale.total'))
                    ->required()
                    ->numeric()
                    ->suffix(fn () => \App\Models\Setting::currency())
                    ->disabled()
                    ->dehydrated(),
            ]);
    }
}