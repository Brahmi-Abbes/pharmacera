<?php

namespace App\Filament\Resources\Suppliers\RelationManagers;

use App\Filament\Resources\Batches\BatchResource;
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
                    ->relationship('medicine', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (blank($get('remaining_quantity'))) {
                            $set('remaining_quantity', $state);
                        }
                    }),
                TextInput::make('remaining_quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->lte('quantity')
                    ->validationMessages([
                        'lte' => 'Remaining quantity cannot be more than the quantity received.',
                    ]),
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
                TextColumn::make('medicine.name')
                    ->searchable()
                    ->sortable(),
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
                    ->color(fn ($record) => $record->expiry_badge_color),
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize(fn () => auth()->user()?->hasAnyRole(BatchResource::manageRoles()) ?? false),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn () => auth()->user()?->hasAnyRole(BatchResource::manageRoles()) ?? false),
                DeleteAction::make()
                    ->authorize(fn () => auth()->user()?->hasAnyRole(BatchResource::deleteRoles()) ?? false),
            ]);
    }
}