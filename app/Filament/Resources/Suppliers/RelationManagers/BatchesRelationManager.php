<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'batches';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('medicine_id')
                    ->label(__('pharmacy.batch.medicine'))
                    ->relationship('medicine', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('pharmacy.batch.quantity'))
                    ->required()
                    ->numeric(),
                TextInput::make('remaining_quantity')
                    ->label(__('pharmacy.batch.remaining_quantity'))
                    ->required()
                    ->numeric(),
                TextInput::make('purchase_price')
                    ->label(__('pharmacy.batch.purchase_price'))
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                DatePicker::make('expiry_date')
                    ->label(__('pharmacy.batch.expiry_date'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('medicine.name')
                    ->label(__('pharmacy.batch.medicine'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('pharmacy.batch.quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_quantity')
                    ->label(__('pharmacy.batch.remaining_quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->label(__('pharmacy.batch.purchase_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label(__('pharmacy.batch.expiry_date'))
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match ($record->expiry_status) {
                        'expired' => 'danger',
                        'danger' => 'danger',
                        'warning' => 'warning',
                        default => 'success',
                    }),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}