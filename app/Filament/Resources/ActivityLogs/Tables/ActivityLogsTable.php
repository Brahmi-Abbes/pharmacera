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
                    ->label('When')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('log_name')
                    ->label('Area')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'sale' => 'success',
                        'sale_item' => 'info',
                        'batch' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('description')
                    ->label('Event')
                    ->badge(),
                TextColumn::make('subject_id')
                    ->label('Record #'),
                TextColumn::make('causer.name')
                    ->label('By')
                    ->placeholder('System'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Area')
                    ->options([
                        'sale' => 'Sale',
                        'sale_item' => 'Sale item',
                        'batch' => 'Batch',
                    ]),
                SelectFilter::make('description')
                    ->label('Event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
