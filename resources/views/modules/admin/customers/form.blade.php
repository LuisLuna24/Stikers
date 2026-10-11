<x-layouts::app :title="__('Formulario cliente')">
    @livewire('modules.admin.customers.form', ['id' => request()->route('id')])
</x-layouts::app>