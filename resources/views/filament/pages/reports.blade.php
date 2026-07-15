<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                Generate PDF
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>