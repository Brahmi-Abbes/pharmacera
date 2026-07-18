<?php

namespace App\Filament\Resources\Users;

use App\Filament\Concerns\HasRoleAuthorization;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    use HasRoleAuthorization;

    protected static function viewRoles(): array
    {
        return ['admin'];
    }

    protected static function manageRoles(): array
    {
        return ['admin'];
    }

    // deleteRoles() stays ['admin'] from the trait, no need to redeclare it.

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('pharmacy.nav.users');
    }

    public static function getModelLabel(): string
    {
        return __('pharmacy.model.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('pharmacy.nav.users');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    // Same admin-only rule as the trait, plus: don't let an admin delete
    // their own account and lock themselves out of the panel.
    public static function canDelete(Model $record): bool
    {
        return (auth()->user()?->hasAnyRole(static::deleteRoles()) ?? false)
            && auth()->id() !== $record->id;
    }
}