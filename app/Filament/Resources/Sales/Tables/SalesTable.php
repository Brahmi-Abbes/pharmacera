<?php

namespace App\Filament\Resources\Sales\Tables;

use App\Filament\Resources\Sales\SaleResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SalesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('pharmacy.sale.sale_number'))
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('pharmacy.sale.cashier'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('items_count')
                    ->label(__('pharmacy.sale.items'))
                    ->counts('items')
                    ->sortable(),
                TextColumn::make('total')
                    ->label(__('pharmacy.sale.total'))
                    ->money()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label(__('pharmacy.sale.payment_method'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => __('pharmacy.sale.cash'),
                        'card' => __('pharmacy.sale.card'),
                        'insurance' => __('pharmacy.sale.insurance'),
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'card' => 'info',
                        'insurance' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('payment_method')
                    ->label(__('pharmacy.sale.payment_method'))
                    ->options([
                        'cash' => __('pharmacy.sale.cash'),
                        'card' => __('pharmacy.sale.card'),
                        'insurance' => __('pharmacy.sale.insurance'),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn () => auth()->user()?->hasAnyRole(SaleResource::manageRoles()) ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn () => auth()->user()?->hasAnyRole(SaleResource::deleteRoles()) ?? false),
                ]),
            ]);
    }
}