<x-layouts::appcustomer :title="__('Crear orden')">
    @livewire('modules.customer.purchase.form', ['order' => request()->route('order')])
</x-layouts::appcustomer>