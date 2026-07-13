<?php

namespace App\Filament\Widgets;

use App\Models\SaleItem;
use Filament\Widgets\ChartWidget;

class TopSellingMedicines extends ChartWidget
{
    protected ?string $heading = 'Top selling medicines (this month)';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $rows = SaleItem::query()
            ->join('medicines', 'medicines.id', '=', 'sale_items.medicine_id')
            ->whereMonth('sale_items.created_at', now()->month)
            ->whereYear('sale_items.created_at', now()->year)
            ->selectRaw('medicines.name as name, sum(sale_items.quantity) as total_quantity')
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Units sold',
                    'data' => $rows->pluck('total_quantity')->toArray(),
                    'backgroundColor' => '#10b981',
                ],
            ],
            'labels' => $rows->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}