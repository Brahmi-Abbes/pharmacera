<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('pharmacy.supplier.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label(__('pharmacy.supplier.phone'))
                    ->tel()
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('wilaya')
                    ->label(__('pharmacy.supplier.wilaya'))
                    ->maxLength(255)
                    ->default(null),
                TextInput::make('email')
                    ->label(__('pharmacy.supplier.email'))
                    ->email()
                    ->maxLength(255)
                    ->default(null),
            ]);
    }
}