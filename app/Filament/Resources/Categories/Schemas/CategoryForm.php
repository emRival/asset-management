<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Classification')
                    ->description('Organize categories within divisions.')
                    ->schema([
                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('prefix_code')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true),
                    ])->columns(2),
            ]);
    }
}
