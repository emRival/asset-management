<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AssetConditionChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Asset Conditions';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $tenantId = Filament::getTenant()->id;

        // Count assets grouped by their condition
        $conditions = Asset::where('unit_id', $tenantId)
            ->select('condition', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('condition')
            ->pluck('count', 'condition')
            ->toArray();

        // Default conditions to show even if they are 0
        $labels = ['Good', 'In Use', 'Maintenance', 'Written Off'];
        $data = [];

        foreach ($labels as $label) {
            $data[] = $conditions[$label] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Assets',
                    'data' => $data,
                    'backgroundColor' => [
                        '#10b981', // green for Good
                        '#3b82f6', // blue for In Use
                        '#f59e0b', // amber for Maintenance
                        '#ef4444', // red for Written Off
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
