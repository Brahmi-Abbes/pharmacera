<?php

namespace App\Filament\Resources\Medicines;

use App\Filament\Resources\Medicines\Pages\CreateMedicine;
use App\Filament\Resources\Medicines\Pages\EditMedicine;
use App\Filament\Resources\Medicines\Pages\ListMedicines;
use App\Filament\Resources\Medicines\RelationManagers\BatchesRelationManager;
use App\Filament\Resources\Medicines\Schemas\MedicineForm;
use App\Filament\Resources\Medicines\Tables\MedicinesTable;
use App\Models\Medicine;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MedicineResource extends Resource
{
    protected static ?string $model = Medicine::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('pharmacy.nav.medicines');
    }

    public static function getModelLabel(): string
    {
        return __('pharmacy.model.medicine');
    }

    public static function form(Schema $schema): Schema
    {
        return MedicineForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicinesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            BatchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicines::route('/'),
            'create' => CreateMedicine::route('/create'),
            'edit' => EditMedicine::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'pharmacist', 'cashier']) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'pharmacist']) ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'pharmacist']) ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
    public static function canDeleteAny(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}