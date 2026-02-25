<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Asset;
use App\Models\Unit;
use Filament\Widgets\ChartWidget;

class AssetsPerUnitChart extends ChartWidget
{
    protected ?string $heading = 'Assets Distribution by Unit';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Get units and their asset counts
        $units = Unit::withCount('assets')->get();

        $labels = $units->pluck('name')->toArray();
        $data = $units->pluck('assets_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $data,
                    'backgroundColor' => '#f59e0b', // Amber
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
