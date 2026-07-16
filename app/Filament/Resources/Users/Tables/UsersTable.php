<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('pharmacy.user.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('pharmacy.user.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label(__('pharmacy.user.role'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('pharmacy.user.role_' . $state))
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'pharmacist' => 'info',
                        'cashier' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label(__('pharmacy.category.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label(__('pharmacy.user.role'))
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => __('pharmacy.user.role_' . $record->name)),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn () => auth()->user()?->hasRole('admin') ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn () => auth()->user()?->hasRole('admin') ?? false),
                ]),
            ]);
    }
}