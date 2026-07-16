<?php

namespace App\Filament\Widgets;

use App\Models\Medicine;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockMedicines extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('pharmacy.dashboard.low_stock_medicines'))
            ->query(
                Medicine::query()
                    ->withSum('batches as stock_sum', 'remaining_quantity')
                    ->whereRaw(
                        '(select coalesce(sum(remaining_quantity), 0) from batches where batches.medicine_id = medicines.id) <= medicines.alert_threshold'
                    )
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('pharmacy.medicine.name'))
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label(__('pharmacy.medicine.category'))
                    ->placeholder(__('pharmacy.medicine.uncategorized')),
                TextColumn::make('stock_sum')
                    ->label(__('pharmacy.medicine.stock'))
                    ->badge()
                    ->color('danger'),
                TextColumn::make('alert_threshold')
                    ->label(__('pharmacy.medicine.alert_threshold')),
            ])
            ->paginated([5, 10, 25]);
    }
}