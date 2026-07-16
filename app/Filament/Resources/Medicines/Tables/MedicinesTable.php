<?php

namespace App\Filament\Resources\Medicines\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MedicinesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->withSum('batches as stock_sum', 'remaining_quantity')
            )
            ->columns([
                TextColumn::make('category.name')
                    ->label(__('pharmacy.medicine.category'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('pharmacy.medicine.uncategorized')),
                TextColumn::make('name')
                    ->label(__('pharmacy.medicine.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('generic_name')
                    ->label(__('pharmacy.medicine.generic_name'))
                    ->searchable(),
                TextColumn::make('unit')
                    ->label(__('pharmacy.medicine.unit'))
                    ->searchable(),
                TextColumn::make('selling_price')
                    ->label(__('pharmacy.medicine.selling_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('purchase_price')
                    ->label(__('pharmacy.medicine.purchase_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('stock_sum')
                    ->label(__('pharmacy.medicine.stock'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => (int) $record->stock_sum <= $record->alert_threshold ? 'danger' : 'success'),
                TextColumn::make('alert_threshold')
                    ->label(__('pharmacy.medicine.alert_threshold'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('barcode')
                    ->label(__('pharmacy.medicine.barcode'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('pharmacy.category.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('pharmacy.category.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn () => auth()->user()?->hasAnyRole(['admin', 'pharmacist']) ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn () => auth()->user()?->hasRole('admin') ?? false),
                ]),
            ]);
    }
}