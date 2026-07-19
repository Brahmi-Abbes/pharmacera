<?php

namespace App\Filament\Resources\Suppliers\Tables;

use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('pharmacy.supplier.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('pharmacy.supplier.phone'))
                    ->searchable(),
                TextColumn::make('wilaya')
                    ->label(__('pharmacy.supplier.wilaya'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('pharmacy.supplier.email'))
                    ->searchable(),
                TextColumn::make('batches_count')
                    ->label(__('pharmacy.supplier.batches_count'))
                    ->counts('batches')
                    ->sortable(),
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
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn () => auth()->user()?->hasAnyRole(SupplierResource::manageRoles()) ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn () => auth()->user()?->hasAnyRole(SupplierResource::deleteRoles()) ?? false),
                ]),
            ]);
    }
}