<x-filament-panels::page>

    @livewire(\App\Livewire\ActualPlanOverview::class)



    <x-filament::section>
        <x-slot name="heading">
           Realizar Subscripción
        </x-slot>

        <x-filament-panels::form wire:submit.prevent="save">
            {{ $this->form }}
            <x-filament-panels::form.actions :actions="$this->getFormActions()" />
        </x-filament-panels::form>

    </x-filament::section>


    {{ $this->table }}
</x-filament-panels::page>
