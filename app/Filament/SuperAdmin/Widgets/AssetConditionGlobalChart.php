<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;

class AssetConditionGlobalChart extends ChartWidget
{
    protected ?string $heading = 'System-wide Asset Conditions';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $conditions = Asset::selectRaw('condition, count(*) as count')->groupBy('condition')->get();
        $labels = $conditions->pluck('condition')->map(fn($c) => ucfirst($c))->toArray();
        $data = $conditions->pluck('count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Assets',
                    'data' => $data,
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444', '#3b82f6', '#8b5cf6', '#64748b'], // Professional palette
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
