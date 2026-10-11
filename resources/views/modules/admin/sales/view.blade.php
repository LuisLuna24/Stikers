<x-layouts::app :title="__('Ver venta')">
    @livewire('modules.admin.sales.view', ['id' => request()->route('id')])
</x-layouts::app>