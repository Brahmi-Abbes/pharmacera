<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Centralizes the "which roles can do what" logic that was previously
 * copy-pasted, nearly identically, into every Resource class.
*/
trait HasRoleAuthorization
{
    /** @return string[] Roles allowed to view this resource's list/records. */
    public static function viewRoles(): array
    {
        return ['admin'];
    }

    /** @return string[] Roles allowed to create/edit records. */
    public static function manageRoles(): array
    {
        return ['admin'];
    }

    /** @return string[] Roles allowed to delete records (single or bulk). */
    public static function deleteRoles(): array
    {
        return ['admin'];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(static::viewRoles()) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(static::manageRoles()) ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasAnyRole(static::manageRoles()) ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasAnyRole(static::deleteRoles()) ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasAnyRole(static::deleteRoles()) ?? false;
    }
}