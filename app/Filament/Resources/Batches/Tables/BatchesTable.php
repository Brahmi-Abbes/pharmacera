<?php

namespace App\Filament\Resources\Batches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('medicine.name')
                    ->label('Medicine')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_quantity')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->remaining_quantity <= 0 ? 'danger' : 'success'),
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
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('expiry_date')
            ->filters([
                SelectFilter::make('medicine_id')
                    ->label('Medicine')
                    ->relationship('medicine', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('remaining_quantity')
                    ->label('Stock status')
                    ->placeholder('All')
                    ->trueLabel('In stock')
                    ->falseLabel('Empty')
                    ->queries(
                        true: fn ($query) => $query->where('remaining_quantity', '>', 0),
                        false: fn ($query) => $query->where('remaining_quantity', '<=', 0),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
