<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Operación</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">Pedidos y ventas</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Consulta el avance, los stickers y el pago de cada pedido.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
            <x-w-input wire:model.live.debounce.300ms="search" placeholder="Buscar pedido o cliente..." icon="magnifying-glass" />
            <select wire:model.live="status" aria-label="Filtrar pedidos por estado" class="min-h-10 rounded-lg border-zinc-300 bg-white px-3 text-sm text-zinc-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200">
                <option value="">Todos los estados</option>
                @foreach ($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900"><p class="text-sm text-zinc-500 dark:text-zinc-400">Pedidos registrados</p><p class="mt-2 text-2xl font-semibold tabular-nums text-zinc-950 dark:text-white">{{ number_format($summary['total']) }}</p></div>
        <div class="rounded-xl border border-blue-200 bg-blue-50/60 p-5 shadow-sm dark:border-blue-400/20 dark:bg-blue-400/10"><p class="text-sm text-blue-700 dark:text-blue-300">En producción</p><p class="mt-2 text-2xl font-semibold tabular-nums text-blue-950 dark:text-blue-100">{{ number_format($summary['production']) }}</p></div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/60 p-5 shadow-sm dark:border-amber-400/20 dark:bg-amber-400/10"><p class="text-sm text-amber-700 dark:text-amber-300">Esperando anticipo</p><p class="mt-2 text-2xl font-semibold tabular-nums text-amber-950 dark:text-amber-100">{{ number_format($summary['pending']) }}</p></div>
    </div>

    <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"><div><h2 class="font-semibold text-zinc-950 dark:text-white">Pedidos</h2><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $orders->total() }} resultados encontrados</p></div></div>
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50/80 dark:bg-zinc-800/60"><tr class="text-left text-xs font-medium uppercase tracking-wide text-zinc-500"><th class="px-5 py-3">Pedido</th><th class="px-5 py-3">Cliente</th><th class="px-5 py-3">Stickers</th><th class="px-5 py-3">Estado</th><th class="px-5 py-3">Pago</th><th class="px-5 py-3">Factura</th><th class="px-5 py-3"></th></tr></thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($orders as $order)
                        <tr wire:key="order-{{ $order->id }}" class="transition hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40"><td class="whitespace-nowrap px-5 py-4"><p class="font-semibold text-zinc-900 dark:text-white">{{ $order->order_number }}</p><p class="mt-1 text-xs text-zinc-500">{{ optional($order->requested_at)->format('d/m/Y · H:i') }}</p></td><td class="px-5 py-4"><p class="max-w-48 truncate font-medium text-zinc-900 dark:text-white">{{ $order->customer?->contact_name ?: $order->customer?->user?->name ?: 'Sin nombre' }}</p><p class="mt-1 max-w-48 truncate text-xs text-zinc-500">{{ $order->customer?->email ?: 'Sin correo' }}</p></td><td class="whitespace-nowrap px-5 py-4"><p class="font-semibold tabular-nums text-zinc-900 dark:text-white">{{ number_format((int) $order->items_sum_quantity) }}</p><p class="mt-1 text-xs text-zinc-500">piezas</p></td><td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $this->statusClasses($order->status) }}">{{ $this->statusLabel($order->status) }}</span></td><td class="whitespace-nowrap px-5 py-4"><p class="font-medium tabular-nums text-zinc-900 dark:text-white">${{ number_format((float) $order->total_amount, 2) }}</p><p class="mt-1 text-xs {{ (float) $order->balance_due > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ (float) $order->balance_due > 0 ? 'Saldo: $'.number_format((float) $order->balance_due, 2) : 'Pagado' }}</p></td><td class="whitespace-nowrap px-5 py-4"><span class="inline-flex items-center gap-1.5 text-sm {{ $order->invoiceRequest ? 'text-emerald-700 dark:text-emerald-300' : 'text-zinc-500' }}"><span class="size-1.5 rounded-full {{ $order->invoiceRequest ? 'bg-emerald-500' : 'bg-zinc-400' }}" aria-hidden="true"></span>{{ $order->invoiceRequest ? 'Solicitada' : 'No requerida' }}</span></td><td class="whitespace-nowrap px-5 py-4 text-right"><a href="{{ route('admin.sales.view', $order->id) }}" wire:navigate class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-medium text-indigo-700 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-indigo-300 dark:hover:bg-indigo-400/10">Ver pedido <flux:icon.arrow-right variant="mini" aria-hidden="true" /></a></td></tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-14 text-center"><div class="mx-auto flex size-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 dark:bg-zinc-800"><flux:icon.shopping-bag variant="mini" aria-hidden="true" /></div><p class="mt-4 font-medium text-zinc-900 dark:text-white">No hay pedidos que coincidan</p><p class="mt-1 text-sm text-zinc-500">Prueba con otro cliente, número de pedido o estado.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-zinc-200 md:hidden dark:divide-zinc-700">
            @forelse ($orders as $order)
                <article wire:key="mobile-order-{{ $order->id }}" class="space-y-4 p-5"><div class="flex items-start justify-between gap-3"><div><p class="font-semibold text-zinc-900 dark:text-white">{{ $order->order_number }}</p><p class="mt-1 text-xs text-zinc-500">{{ optional($order->requested_at)->format('d/m/Y · H:i') }}</p></div><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $this->statusClasses($order->status) }}">{{ $this->statusLabel($order->status) }}</span></div><div class="grid grid-cols-2 gap-4 text-sm"><div><p class="text-xs uppercase tracking-wide text-zinc-500">Cliente</p><p class="mt-1 truncate font-medium text-zinc-900 dark:text-white">{{ $order->customer?->contact_name ?: $order->customer?->user?->name ?: 'Sin nombre' }}</p></div><div><p class="text-xs uppercase tracking-wide text-zinc-500">Stickers</p><p class="mt-1 font-semibold tabular-nums text-zinc-900 dark:text-white">{{ number_format((int) $order->items_sum_quantity) }} piezas</p></div><div><p class="text-xs uppercase tracking-wide text-zinc-500">Total</p><p class="mt-1 font-medium tabular-nums text-zinc-900 dark:text-white">${{ number_format((float) $order->total_amount, 2) }}</p></div><div><p class="text-xs uppercase tracking-wide text-zinc-500">Factura</p><p class="mt-1 {{ $order->invoiceRequest ? 'text-emerald-700 dark:text-emerald-300' : 'text-zinc-500' }}">{{ $order->invoiceRequest ? 'Solicitada' : 'No requerida' }}</p></div></div><a href="{{ route('admin.sales.view', $order->id) }}" wire:navigate class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-zinc-200 text-sm font-medium text-indigo-700 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:text-indigo-300 dark:hover:bg-indigo-400/10">Ver pedido <flux:icon.arrow-right variant="mini" aria-hidden="true" /></a></article>
            @empty
                <div class="p-12 text-center text-sm text-zinc-500">No hay pedidos que coincidan con los filtros.</div>
            @endforelse
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-zinc-200 px-5 py-4 dark:border-zinc-700">{{ $orders->links() }}</div>
        @endif
    </section>
</div>
