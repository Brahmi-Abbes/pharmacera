<?php

namespace App\Filament\Widgets;

use App\Models\Batch;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ExpiringBatches extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('pharmacy.dashboard.expiring_soon_heading'))
            ->query(
                Batch::query()
                    ->where('remaining_quantity', '>', 0)
                    ->whereBetween('expiry_date', [today(), today()->addDays(90)])
                    ->orderBy('expiry_date')
            )
            ->columns([
                TextColumn::make('medicine.name')
                    ->label(__('pharmacy.batch.medicine'))
                    ->searchable(),
                TextColumn::make('remaining_quantity')
                    ->label(__('pharmacy.dashboard.qty_left')),
                TextColumn::make('expiry_date')
                    ->label(__('pharmacy.batch.expiry_date'))
                    ->date()
                    ->badge()
                    ->color(fn ($record) => $record->expiry_badge_color),
            ])
            ->paginated([5, 10, 25]);
    }
}