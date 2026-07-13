<?php

namespace App\Filament\Widgets;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class SalesOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todayTotal = Sale::whereDate('created_at', today())->sum('total');
        $weekTotal = Sale::where('created_at', '>=', now()->startOfWeek())->sum('total');

        $lowStockCount = Medicine::query()
            ->withSum('batches as stock_sum', 'remaining_quantity')
            ->get()
            ->filter(fn (Medicine $medicine) => (int) $medicine->stock_sum <= $medicine->alert_threshold)
            ->count();

        $expiringSoonCount = Batch::query()
            ->where('remaining_quantity', '>', 0)
            ->whereBetween('expiry_date', [today(), today()->addDays(90)])
            ->count();

        return [
            Stat::make("Today's sales", number_format($todayTotal, 2).' $')
                ->description('All sales recorded today')
                ->color('success'),

            Stat::make('This week', number_format($weekTotal, 2).' $')
                ->description('Since Monday')
                ->color('success'),

            Stat::make('Low stock medicines', $lowStockCount)
                ->description('At or below their alert threshold')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make('Batches expiring soon', $expiringSoonCount)
                ->description('Within the next 90 days')
                ->color($expiringSoonCount > 0 ? 'warning' : 'success'),
        ];
    }
}
