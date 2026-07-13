<?php

namespace App\Filament\Resources\Batches\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BatchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('medicine_id')
                    ->label('Medicine')
                    ->relationship('medicine', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // Default remaining_quantity to quantity when creating a new batch,
                        // unless the user has already typed a different value.
                        if (blank($get('remaining_quantity'))) {
                            $set('remaining_quantity', $state);
                        }
                    }),
                TextInput::make('remaining_quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    // Can never physically have more left than you originally received.
                    ->lte('quantity')
                    ->validationMessages([
                        'lte' => 'Remaining quantity cannot be more than the quantity received.',
                    ]),
                TextInput::make('purchase_price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$'),
                DatePicker::make('expiry_date')
                    ->required()
                    ->native(false),
            ]);
    }
}