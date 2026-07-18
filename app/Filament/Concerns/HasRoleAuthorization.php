<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Centralizes the "which roles can do what" logic that was previously
 * copy-pasted, nearly identically, into every Resource class.
 *
 * Note: these are methods, not properties. Traits and PHP don't get along
 * when you try to override a trait's property default from the class using
 * it (PHP treats that as a fatal conflict, not a normal override) — methods
 * don't have that problem, so that's what we use here instead.
 *
 * A resource just overrides viewRoles()/manageRoles()/deleteRoles() with
 * whatever roles actually apply. If a resource needs one more rule on top
 * (like UserResource's "can't delete yourself"), redeclare that specific
 * public method in the resource itself — same deal, no conflict.
 */
trait HasRoleAuthorization
{
    /** @return string[] Roles allowed to view this resource's list/records. */
    protected static function viewRoles(): array
    {
        return ['admin'];
    }

    /** @return string[] Roles allowed to create/edit records. */
    protected static function manageRoles(): array
    {
        return ['admin'];
    }

    /** @return string[] Roles allowed to delete records (single or bulk). */
    protected static function deleteRoles(): array
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