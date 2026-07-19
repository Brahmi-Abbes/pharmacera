<?php

namespace App\Filament\Resources\Batches\Tables;

use App\Filament\Resources\Batches\BatchResource;
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
                    ->label(__('pharmacy.batch.medicine'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('supplier.name')
                    ->label(__('pharmacy.batch.supplier'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('quantity')
                    ->label(__('pharmacy.batch.quantity'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('remaining_quantity')
                    ->label(__('pharmacy.batch.remaining_quantity'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->remaining_quantity <= 0 ? 'danger' : 'success'),
                TextColumn::make('purchase_price')
                    ->label(__('pharmacy.batch.purchase_price'))
                    ->money()
                    ->sortable(),
                TextColumn::make('expiry_date')
                    ->label(__('pharmacy.batch.expiry_date'))
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->expiry_badge_color),
                TextColumn::make('created_at')
                    ->label(__('pharmacy.common.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('pharmacy.common.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('expiry_date')
            ->filters([
                SelectFilter::make('medicine_id')
                    ->label(__('pharmacy.batch.medicine'))
                    ->relationship('medicine', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('remaining_quantity')
                    ->label(__('pharmacy.batch.stock_status'))
                    ->placeholder(__('pharmacy.batch.all'))
                    ->trueLabel(__('pharmacy.batch.in_stock'))
                    ->falseLabel(__('pharmacy.batch.empty'))
                    ->queries(
                        true: fn ($query) => $query->where('remaining_quantity', '>', 0),
                        false: fn ($query) => $query->where('remaining_quantity', '<=', 0),
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn () => auth()->user()?->hasAnyRole(BatchResource::manageRoles()) ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn () => auth()->user()?->hasAnyRole(BatchResource::deleteRoles()) ?? false),
                ]),
            ]);
    }
}