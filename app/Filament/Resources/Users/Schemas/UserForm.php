<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('pharmacy.user.name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label(__('pharmacy.user.email'))
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label(__('pharmacy.user.password'))
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->minLength(8)
                    ->same('passwordConfirmation')
                    ->helperText(__('pharmacy.user.password_help')),
                TextInput::make('passwordConfirmation')
                    ->label(__('pharmacy.user.password_confirmation'))
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(false),
                Select::make('roles')
                    ->label(__('pharmacy.user.role'))
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => __('pharmacy.user.role_' . $record->name))
                    ->required()
                    ->preload()
                    ->searchable(),
            ]);
    }
}