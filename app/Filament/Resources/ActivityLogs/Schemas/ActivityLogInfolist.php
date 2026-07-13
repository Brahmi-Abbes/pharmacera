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
                Section::make('Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('log_name')
                            ->label('Area')
                            ->badge(),
                        TextEntry::make('description')
                            ->label('Event')
                            ->badge(),
                        TextEntry::make('causer.name')
                            ->label('Changed by')
                            ->placeholder('System'),
                        TextEntry::make('created_at')
                            ->label('When')
                            ->dateTime(),
                        TextEntry::make('subject_type')
                            ->label('Model'),
                        TextEntry::make('subject_id')
                            ->label('Record #'),
                    ]),
                Section::make('What changed')
                    ->schema([
                        KeyValueEntry::make('properties.old')
                            ->label('Before')
                            ->visible(fn ($record) => filled($record->properties['old'] ?? null)),
                        KeyValueEntry::make('properties.attributes')
                            ->label('After')
                            ->visible(fn ($record) => filled($record->properties['attributes'] ?? null)),
                    ]),
            ]);
    }
}
