<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentAssetsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Recently Added Assets';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder =>
                Asset::query()
                    ->where('unit_id', Filament::getTenant()->id)
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('asset_code')
                    ->label('Asset Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                TextColumn::make('condition')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Good' => 'success',
                        'In Use' => 'info',
                        'Maintenance' => 'warning',
                        'Written Off' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Added On')
                    ->dateTime()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
