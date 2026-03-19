<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Division;
use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GlobalStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Units', Unit::count())
                ->description('Registered organizational units')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->chart($this->getTrend(Unit::class))
                ->color('primary'),

            Stat::make('Total Divisions', Division::count())
                ->description('Active divisions across units')
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($this->getTrend(Division::class))
                ->color('success'),

            Stat::make('Total Categories', Category::count())
                ->description('Asset classifications')
                ->descriptionIcon('heroicon-m-tag')
                ->chart($this->getTrend(Category::class))
                ->color('warning'),

            Stat::make('Total Assets', Asset::count())
                ->description('Total physical items tracked system-wide')
                ->descriptionIcon('heroicon-m-cube')
                ->chart($this->getTrend(Asset::class))
                ->color('danger'),
        ];
    }

    private function getTrend(string $model): array
    {
        return collect(range(0, 6))->map(function ($i) use ($model) {
            return $model::whereDate('created_at', now()->subDays($i))->count();
        })->reverse()->values()->toArray();
    }
}
