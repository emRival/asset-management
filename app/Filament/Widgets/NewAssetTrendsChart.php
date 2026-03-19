<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Filament\Facades\Filament;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NewAssetTrendsChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Asset Registration Trends';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $tenantId = Filament::getTenant()?->id ?? auth()->user()->unit_id;
        
        // Last 6 months
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('M Y');
        })->reverse()->values();

        $data = $months->map(function ($monthLabel) use ($tenantId) {
            $date = Carbon::parse($monthLabel);
            return Asset::where('unit_id', $tenantId)
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'New Assets',
                    'data' => $data,
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#8b5cf6', // purple
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
