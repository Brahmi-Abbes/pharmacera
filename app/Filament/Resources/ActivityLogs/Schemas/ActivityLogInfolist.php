<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityLogInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('pharmacy.activity.details'))
                    ->columns(2)
                    ->schema([
                        TextEntry::make('log_name')
                            ->label(__('pharmacy.activity.area'))
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'sale' => __('pharmacy.model.sale'),
                                'sale_item' => __('pharmacy.activity.sale_item'),
                                'batch' => __('pharmacy.model.batch'),
                                default => (string) $state,
                            }),
                        TextEntry::make('description')
                            ->label(__('pharmacy.activity.event'))
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'created' => __('pharmacy.activity.created'),
                                'updated' => __('pharmacy.activity.updated'),
                                'deleted' => __('pharmacy.activity.deleted'),
                                default => (string) $state,
                            }),
                        TextEntry::make('causer.name')
                            ->label(__('pharmacy.activity.changed_by'))
                            ->placeholder(__('pharmacy.activity.system')),
                        TextEntry::make('created_at')
                            ->label(__('pharmacy.activity.when'))
                            ->dateTime(),
                        TextEntry::make('subject_type')
                            ->label(__('pharmacy.activity.model'))
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'App\\Models\\Sale' => __('pharmacy.model.sale'),
                                'App\\Models\\SaleItem' => __('pharmacy.activity.sale_item'),
                                'App\\Models\\Batch' => __('pharmacy.model.batch'),
                                default => (string) $state,
                            }),
                        TextEntry::make('subject_id')
                            ->label(__('pharmacy.activity.record_number')),
                    ]),
                Section::make(__('pharmacy.activity.what_changed'))
                    ->schema([
                        KeyValueEntry::make('attribute_changes.old')
                            ->label(__('pharmacy.activity.before'))
                            ->visible(fn ($record) => filled($record->attribute_changes['old'] ?? null)),
                        KeyValueEntry::make('attribute_changes.attributes')
                            ->label(__('pharmacy.activity.after'))
                            ->visible(fn ($record) => filled($record->attribute_changes['attributes'] ?? null)),
                    ]),
            ]);
    }
}