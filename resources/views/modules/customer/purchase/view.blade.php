<x-layouts::appcustomer :title="__('Detalle del pedido')">
    @livewire('modules.customer.purchase.view', ['orderId' => request()->route('order')])
</x-layouts::appcustomer>
