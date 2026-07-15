<?php

namespace App\Filament\Resources\Medicines\RelationManagers;

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
                Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('remaining_quantity')
                    ->required()
                    ->numeric(),
                TextInput::make('purchase_price')
                    ->required()
                    ->numeric()
                    ->prefix('DZD '),
                DatePicker::make('expiry_date')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => match ($record->expiry_status) {
                        'expired', 'danger' => 'danger',
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