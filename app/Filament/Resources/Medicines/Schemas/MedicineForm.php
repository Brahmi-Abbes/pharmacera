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
                    ->label(__('pharmacy.medicine.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(__('pharmacy.category.name'))
                            ->required()
                            ->unique(),
                    ])
                    ->nullable(),
                TextInput::make('name')
                    ->label(__('pharmacy.medicine.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('barcode')
                    ->label(__('pharmacy.medicine.barcode'))
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('generic_name')
                    ->label(__('pharmacy.medicine.generic_name'))
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('unit')
                    ->label(__('pharmacy.medicine.unit'))
                    ->required()
                    ->default('box')
                    ->maxLength(255),
                TextInput::make('selling_price')
                    ->label(__('pharmacy.medicine.selling_price'))
                    ->required()
                    ->numeric()
                    ->suffix(fn () => \App\Models\Setting::currency()),
                TextInput::make('purchase_price')
                    ->label(__('pharmacy.medicine.purchase_price'))
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->suffix(fn () => \App\Models\Setting::currency()),
                TextInput::make('alert_threshold')
                    ->label(__('pharmacy.medicine.alert_threshold'))
                    ->required()
                    ->numeric()
                    ->default(10),
            ]);
    }
}