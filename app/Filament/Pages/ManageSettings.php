<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSettings extends Page
{
    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function getNavigationLabel(): string
    {
        return __('pharmacy.nav.settings');
    }

    public function getTitle(): string
    {
        return __('pharmacy.settings.title');
    }

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill($this->getRecord()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('store_name')
                    ->label(__('pharmacy.settings.store_name'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('currency')
                    ->label(__('pharmacy.settings.currency'))
                    ->required()
                    ->maxLength(10)
                    ->helperText(__('pharmacy.settings.currency_help')),
                TextInput::make('phone')
                    ->label(__('pharmacy.settings.phone'))
                    ->tel()
                    ->maxLength(255),
                TextInput::make('address')
                    ->label(__('pharmacy.settings.address'))
                    ->maxLength(255),
                TextInput::make('tax_rate')
                    ->label(__('pharmacy.settings.tax_rate'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->suffix('%'),
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function getRecord(): Setting
    {
        return Setting::current();
    }

    public function save(): void
    {
        $this->getRecord()->update($this->form->getState());

        Notification::make()
            ->title(__('pharmacy.settings.saved_title'))
            ->success()
            ->send();
    }
}