<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivityLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('pharmacy.activity.when'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('log_name')
                    ->label(__('pharmacy.activity.area'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'sale' => __('pharmacy.model.sale'),
                        'sale_item' => __('pharmacy.activity.sale_item'),
                        'batch' => __('pharmacy.model.batch'),
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'sale' => 'success',
                        'sale_item' => 'info',
                        'batch' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->label(__('pharmacy.activity.event'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'created' => __('pharmacy.activity.created'),
                        'updated' => __('pharmacy.activity.updated'),
                        'deleted' => __('pharmacy.activity.deleted'),
                        default => (string) $state,
                    }),
                TextColumn::make('subject_id')
                    ->label(__('pharmacy.activity.record_number')),
                TextColumn::make('causer.name')
                    ->label(__('pharmacy.activity.by'))
                    ->placeholder(__('pharmacy.activity.system')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('log_name')
                    ->label(__('pharmacy.activity.area'))
                    ->options([
                        'sale' => __('pharmacy.model.sale'),
                        'sale_item' => __('pharmacy.activity.sale_item'),
                        'batch' => __('pharmacy.model.batch'),
                    ]),
                SelectFilter::make('description')
                    ->label(__('pharmacy.activity.event'))
                    ->options([
                        'created' => __('pharmacy.activity.created'),
                        'updated' => __('pharmacy.activity.updated'),
                        'deleted' => __('pharmacy.activity.deleted'),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn () => auth()->user()?->hasRole('admin') ?? false),
                ]),
            ]);
    }
}