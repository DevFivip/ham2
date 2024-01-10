<x-filament-panels::page>

    <x-filament-panels::form wire:submit.prevent="save">
        {{ $this->form }}
        <x-filament-panels::form.actions :actions="$this->getFormActions()" />
    </x-filament-panels::form>

    @livewire(\App\Filament\Widgets\CalendarWidget::class, ['widgetData'=>$widgetData])
</x-filament-panels::page>
