<?php

namespace App\Filament\Resources\Assets\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'activities';
    protected static ?string $title = 'Audit History (Auto)';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('causer.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Event')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('properties')
                    ->label('Changes')
                    ->wrap()
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return '-';

                        $output = [];
                        $attributes = $state['attributes'] ?? [];
                        $old = $state['old'] ?? [];

                        if (!empty($attributes)) {
                            foreach ($attributes as $key => $value) {
                                // Skip arrays or objects
                                if (is_array($value) || is_object($value))
                                    continue;
                                $oldValue = $old[$key] ?? '(empty)';
                                if (is_array($oldValue) || is_object($oldValue))
                                    $oldValue = '...';
                                $output[] = "<strong>{$key}</strong>: <s>{$oldValue}</s> <span class='text-primary-600 dark:text-primary-400'>&rarr; {$value}</span>";
                            }
                        } else {
                            // Non-update events (like create) or custom properties Without attributes/old keys
                            foreach ($state as $key => $value) {
                                if (is_scalar($value)) {
                                    $output[] = "<strong>{$key}</strong>: {$value}";
                                }
                            }
                        }

                        return empty($output) ? '-' : new HtmlString(implode('<br>', $output));
                    }),
            ]);
    }
}
