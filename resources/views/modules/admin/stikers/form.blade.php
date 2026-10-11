<x-layouts::app :title="__('Formulario de stiker')">
    @livewire('modules.admin.stikers.form', ['id' => request()->route('id')])
</x-layouts::app>