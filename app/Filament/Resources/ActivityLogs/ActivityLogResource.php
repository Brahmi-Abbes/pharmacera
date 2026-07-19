<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Concerns\HasRoleAuthorization;
use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLog;
use App\Filament\Resources\ActivityLogs\Schemas\ActivityLogInfolist;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    use HasRoleAuthorization;

    // Read-only for everyone — admin and pharmacist can look, nobody creates
    // or edits a log entry by hand, and only admin can clear old ones out.
    public static function viewRoles(): array
    {
        return ['admin', 'pharmacist'];
    }

    public static function manageRoles(): array
    {
        return [];
    }

    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function getNavigationLabel(): string
    {
        return __('pharmacy.nav.activity_log');
    }

    public static function getModelLabel(): string
    {
        return __('pharmacy.nav.activity_log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('pharmacy.nav.activity_log');
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityLogInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
            'view' => ViewActivityLog::route('/{record}'),
        ];
    }
}