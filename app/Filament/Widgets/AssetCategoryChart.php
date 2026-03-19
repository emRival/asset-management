<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;
use Filament\Facades\Filament;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class AssetCategoryChart extends ChartWidget
{
    use HasWidgetShield;
    protected ?string $heading = 'Assets by Category';
    
    protected static ?int $sort = 2;
    
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $tenantId = Filament::getTenant()?->id ?? auth()->user()->unit_id;
        
        $data = Category::whereHas('division', fn($q) => $q->where('unit_id', $tenantId))
            ->withCount(['assets' => fn($q) => $q->where('unit_id', $tenantId)])
            ->get()
            ->filter(fn ($category) => $category->assets_count > 0);

        return [
            'datasets' => [
                [
                    'label' => 'Total Assets',
                    'data' => $data->pluck('assets_count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'
                    ],
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
