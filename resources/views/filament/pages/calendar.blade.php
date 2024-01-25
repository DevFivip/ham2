<x-filament-panels::page>

<style>
    .fc-daygrid-day {
        max-width: 30px;
    }
</style>

    <x-filament::section collapsible collapsed persist-collapsed>
        <x-slot name="heading">
            Filtros
        </x-slot>

        <x-filament-panels::form wire:submit.prevent="save">
            {{ $this->form }}
            <x-filament-panels::form.actions :actions="$this->getFormActions()" />
        </x-filament-panels::form>
    </x-filament::section>

    @livewire(\App\Filament\Widgets\CalendarWidget::class, ['widgetData' => $widgetData])

</x-filament-panels::page>
