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

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Cashier')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'insurance' => 'Insurance',
                    ])
                    ->default('cash')
                    ->required(),
                Repeater::make('items')
                    ->relationship()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $total = collect($get('items'))
                            ->sum(fn ($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0));

                        $set('total', round($total, 2));
                    })
                    ->schema([
                        Select::make('medicine_id')
                            ->label('Medicine')
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
                            ->label('Batch')
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

                                return $remaining === null ? null : "{$remaining} available in this batch.";
                            })
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('subtotal', round((float) $get('quantity') * (float) $get('unit_price'), 2));
                            }),
                        TextInput::make('unit_price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('subtotal', round((float) $get('quantity') * (float) $get('unit_price'), 2));
                            }),
                        TextInput::make('subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(4)
                    ->columnSpanFull()
                    ->minItems(1)
                    ->required(),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }
}
