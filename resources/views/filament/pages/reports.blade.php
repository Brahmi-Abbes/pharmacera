<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <button type="submit" class="fi-btn fi-btn-size-lg fi-color-primary fi-btn-color-primary mt-4">
            Generate PDF
        </button>
    </form>
</x-filament-panels::page>