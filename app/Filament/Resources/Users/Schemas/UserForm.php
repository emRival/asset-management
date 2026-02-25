<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ])->columns(2),
                Section::make('Security & Role')
                    ->schema([
                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->nullable()
                            ->searchable()
                            ->preload(),
                        Select::make('roles')
                            ->multiple()
                            ->options(fn() => \App\Models\Role::query()->where('unit_id', \Filament\Facades\Filament::getTenant()->id)->pluck('name', 'id'))
                            ->afterStateHydrated(function (Select $component, ?\Illuminate\Database\Eloquent\Model $record) {
                                if ($record) {
                                    $component->state($record->roles()->pluck('id')->toArray());
                                }
                            })
                            ->saveRelationshipsUsing(function (\Illuminate\Database\Eloquent\Model $record, $state) {
                                setPermissionsTeamId(\Filament\Facades\Filament::getTenant()->id);
                                $record->syncRoles($state ?? []);
                            })
                            ->dehydrated(false)
                            ->preload()
                            ->searchable(),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),
                    ])->columns(2),
            ]);
    }
}
