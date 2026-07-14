<?php

namespace App\Filament\Resources\Medicines\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MedicineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->unique(),
                    ])
                    ->nullable(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                // MedicineForm.php
                TextInput::make('barcode')->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('generic_name')
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('unit')
                    ->required()
                    ->default('box')
                    ->maxLength(255),
                TextInput::make('selling_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('purchase_price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('alert_threshold')
                    ->required()
                    ->numeric()
                    ->default(10),
            ]);
    }
}
