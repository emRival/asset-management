<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Unit;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class UnitGrowthChart extends ChartWidget
{
    protected ?string $heading = 'Unit Growth (New Tenants)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $months = collect(range(0, 5))->map(function ($i) {
            return now()->subMonths($i)->format('M Y');
        })->reverse()->values();

        $data = $months->map(function ($monthLabel) {
            try {
                $date = Carbon::parse($monthLabel);
                return Unit::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
            } catch (\Exception $e) {
                return 0;
            }
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'New Units',
                    'data' => $data,
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#fbbf24', // amber
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
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
