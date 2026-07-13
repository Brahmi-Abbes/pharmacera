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

    protected int|string|array $columnSpan = [
        'md' => 1,
        'lg' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Low stock medicines')
            ->query(
                Medicine::query()
                    ->withSum('batches as stock_sum', 'remaining_quantity')
                    ->whereRaw(
                        '(select coalesce(sum(remaining_quantity), 0) from batches where batches.medicine_id = medicines.id) <= medicines.alert_threshold'
                    )
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->placeholder('Uncategorized'),
                TextColumn::make('stock_sum')
                    ->label('In stock')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('alert_threshold')
                    ->label('Threshold'),
            ])
            ->paginated([5, 10, 25]);
    }
}
