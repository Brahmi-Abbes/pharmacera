<?php

namespace App\Filament\Pages;

use App\Models\Batch;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class Reports extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.reports';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'period' => 'daily',
            'date'   => now()->toDateString(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('period')
                    ->label('Report period')
                    ->options([
                        'daily'   => 'Daily',
                        'monthly' => 'Monthly',
                    ])
                    ->inline()
                    ->inlineLabel(false)
                    ->default('daily')
                    ->live(),

                DatePicker::make('date')
                    ->label(fn (Get $get) => $get('period') === 'monthly' ? 'Any day in the month' : 'Date')
                    ->default(now())
                    ->native(false)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make('generate')
                ->label('Generate PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action('generate'),
        ];
    }

    public function generate()
    {
        $state = $this->form->getState();

        [$start, $end, $label] = $this->resolveRange($state['period'], $state['date']);

        $data = $this->buildReportData($start, $end);
        $data['label'] = $label;
        $data['generated_at'] = now();
        $data['generated_by'] = auth()->user()?->name;

        $pdf = Pdf::loadView('pdf.report', $data)->setPaper('a4');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'pharmacera-report-' . now()->format('Y-m-d-His') . '.pdf'
        );
    }

    private function resolveRange(string $period, string $date): array
    {
        $day = Carbon::parse($date);

        if ($period === 'monthly') {
            return [
                $day->copy()->startOfMonth(),
                $day->copy()->endOfMonth(),
                $day->format('F Y'),
            ];
        }

        return [
            $day->copy()->startOfDay(),
            $day->copy()->endOfDay(),
            $day->format('F j, Y'),
        ];
    }

    private function buildReportData(Carbon $start, Carbon $end): array
    {
        $sales = Sale::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $totalRevenue = $sales->sum('total');
        $totalSales   = $sales->count();

        $byPaymentMethod = $sales->groupBy('payment_method')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ]);

        $byStaff = $sales->groupBy(fn ($sale) => $sale->user?->name ?? 'Unknown')
            ->map(fn ($group) => [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ])
            ->sortByDesc('total');

        $topMedicines = SaleItem::whereHas('sale', fn ($q) => $q->whereBetween('created_at', [$start, $end]))
            ->with('medicine')
            ->get()
            ->groupBy('medicine_id')
            ->map(fn ($group) => [
                'medicine' => $group->first()->medicine,
                'quantity' => $group->sum('quantity'),
                'revenue'  => $group->sum('subtotal'),
            ])
            ->sortByDesc('quantity')
            ->take(10)
            ->values();

        $stockValue = Batch::where('remaining_quantity', '>', 0)
            ->get()
            ->sum(fn ($batch) => $batch->remaining_quantity * $batch->purchase_price);

        $lowStock = Medicine::all()->filter(fn ($m) => $m->is_low_stock)->values();

        $expiringSoon = Batch::where('remaining_quantity', '>', 0)
            ->whereBetween('expiry_date', [now(), now()->addDays(30)])
            ->with('medicine')
            ->orderBy('expiry_date')
            ->get();

        $expired = Batch::where('remaining_quantity', '>', 0)
            ->where('expiry_date', '<', now())
            ->with('medicine')
            ->get();

        $expiredValue = $expired->sum(fn ($batch) => $batch->remaining_quantity * $batch->purchase_price);

        return [
            'start'           => $start,
            'end'             => $end,
            'totalRevenue'    => $totalRevenue,
            'totalSales'      => $totalSales,
            'averageSale'     => $totalSales > 0 ? $totalRevenue / $totalSales : 0,
            'byPaymentMethod' => $byPaymentMethod,
            'byStaff'         => $byStaff,
            'topMedicines'    => $topMedicines,
            'stockValue'      => $stockValue,
            'lowStock'        => $lowStock,
            'expiringSoon'    => $expiringSoon,
            'expired'         => $expired,
            'expiredValue'    => $expiredValue,
        ];
    }
}