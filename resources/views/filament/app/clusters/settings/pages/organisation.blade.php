<x-filament-panels::page>
    <div>
        <form wire:submit.prevent="save">
            {{ $this->form }}
        </form>
    </div>
</x-filament-panels::page>
