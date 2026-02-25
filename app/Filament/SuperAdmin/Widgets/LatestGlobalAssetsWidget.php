<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Asset;
use Filament\Tables;
use Filament\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestGlobalAssetsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder =>
                Asset::query()->latest()->limit(5)
            )
            ->paginated(false)
            ->heading('Global Asset Feed (Latest 5)')
            ->columns([
                Tables\Columns\TextColumn::make('unit.name')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('asset_code')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Good' => 'success',
                        'In Use' => 'info',
                        'Maintenance' => 'warning',
                        'Written Off' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn(Asset $record): string => route('filament.admin.resources.assets.view', ['tenant' => $record->unit->slug, 'record' => $record->asset_code]))
                    ->openUrlInNewTab(),
            ]);
    }
}
