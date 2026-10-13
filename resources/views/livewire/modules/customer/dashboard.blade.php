<div class="mx-auto max-w-7xl space-y-6">
    <section class="relative overflow-hidden rounded-2xl bg-zinc-950 px-6 py-8 text-white shadow-sm sm:px-8 sm:py-10">
        <div class="relative z-10 max-w-2xl">
            <p class="text-sm font-medium text-indigo-300">Tu espacio Luna Stickers</p>
            <flux:heading size="xl" class="mt-2 text-white">Hola, {{ $customer?->contact_name ?: auth()->user()->name }}</flux:heading>
            <p class="mt-3 max-w-xl text-sm leading-6 text-zinc-300">Consulta tus pedidos, revisa tus pagos y solicita diseños personalizados para tu próximo proyecto.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <flux:button href="{{ route('customer.purchase.create') }}" variant="primary" icon="plus" wire:navigate>
                    Crear un pedido
                </flux:button>
                <flux:button href="{{ route('customer.design-requests.index') }}" variant="ghost" class="text-white hover:bg-white/10 hover:text-white" icon="document-text" wire:navigate>
                    Solicitar un diseño
                </flux:button>
            </div>
        </div>
        <div class="pointer-events-none absolute -right-20 -top-24 size-72 rounded-full bg-indigo-500/25 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-32 right-24 size-64 rounded-full bg-fuchsia-500/20 blur-3xl" aria-hidden="true"></div>
    </section>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.stat-card label="Mis pedidos" :value="number_format($stats['orders'])" detail="Pedidos registrados" icon="shopping-cart" />
        <x-dashboard.stat-card label="En proceso" :value="number_format($stats['active_orders'])" detail="Pedidos con seguimiento" icon="clock" tone="blue" />
        <x-dashboard.stat-card label="Saldo pendiente" :value="'$'.number_format((float) $stats['balance'], 2)" detail="En todos tus pedidos" icon="banknotes" tone="amber" />
        <x-dashboard.stat-card label="Solicitudes activas" :value="number_format($stats['requests'])" detail="Diseños personalizados" icon="document-text" tone="emerald" />
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 border-b border-zinc-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-700">
                <div>
                    <flux:heading size="lg">Pedidos recientes</flux:heading>
                    <flux:text class="mt-1">Da seguimiento a tus compras más recientes.</flux:text>
                </div>
                <flux:button href="{{ route('customer.purchase.index') }}" variant="ghost" icon="arrow-right" wire:navigate>
                    Ver todos
                </flux:button>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($recentOrders as $order)
                    <a wire:key="customer-dashboard-order-{{ $order->id }}" href="{{ route('customer.purchase.show', $order->id) }}" wire:navigate class="flex flex-col gap-3 px-5 py-4 transition hover:bg-zinc-50 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-indigo-600 sm:flex-row sm:items-center sm:justify-between dark:hover:bg-zinc-800/60">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">
                                <flux:icon name="shopping-bag" variant="mini" aria-hidden="true" />
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-zinc-950 dark:text-white">{{ $order->order_number }}</p>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $order->items_count }} {{ $order->items_count === 1 ? 'línea' : 'líneas' }} · {{ $order->requested_at?->format('d/m/Y') ?: 'Fecha pendiente' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 sm:justify-end">
                            <x-dashboard.status-badge :label="$this->statusLabel($order->status)" :classes="$this->statusClasses($order->status)" :dot="$this->statusDotClasses($order->status)" />
                            <span class="font-semibold tabular-nums text-zinc-950 dark:text-white">${{ number_format((float) $order->total_amount, 2) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="p-5">
                        <x-dashboard.empty-state title="Aún no tienes pedidos" description="Crea tu primer pedido personalizado desde esta cuenta." icon="shopping-cart">
                            <flux:button href="{{ route('customer.purchase.create') }}" variant="ghost" icon="plus" wire:navigate>Crear pedido</flux:button>
                        </x-dashboard.empty-state>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-5 dark:border-indigo-400/20 dark:bg-indigo-400/10">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-700 shadow-sm dark:bg-zinc-900 dark:text-indigo-300">
                        <flux:icon name="sparkles" class="size-5" aria-hidden="true" />
                    </span>
                    <div>
                        <h2 class="font-semibold text-zinc-950 dark:text-white">¿Tienes una idea?</h2>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">Solicita un diseño personalizado y cuéntanos lo que necesitas.</p>
                    </div>
                </div>
                <flux:button href="{{ route('customer.design-requests.index') }}" variant="ghost" class="mt-4 w-full" wire:navigate>
                    Solicitar diseño
                </flux:button>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg">Accesos rápidos</flux:heading>
                <div class="mt-4 grid gap-2">
                    <flux:button href="{{ route('customer.purchase.create') }}" variant="ghost" icon="plus-circle" class="justify-start" wire:navigate>Crear pedido</flux:button>
                    <flux:button href="{{ route('customer.purchase.index') }}" variant="ghost" icon="shopping-cart" class="justify-start" wire:navigate>Consultar pedidos</flux:button>
                    <flux:button href="{{ route('customer.design-requests.index') }}" variant="ghost" icon="document-text" class="justify-start" wire:navigate>Solicitudes de diseño</flux:button>
                </div>
            </section>
        </aside>
    </div>

    <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex items-start justify-between gap-4">
            <div>
                <flux:heading size="lg">Mis solicitudes de diseño</flux:heading>
                <flux:text class="mt-1">Revisa el avance de tus ideas personalizadas.</flux:text>
            </div>
            <flux:button href="{{ route('customer.design-requests.index') }}" variant="ghost" icon="arrow-right" wire:navigate>
                Ver solicitudes
            </flux:button>
        </div>
        <div class="mt-4 grid gap-3 md:grid-cols-3">
            @forelse ($recentRequests as $request)
                <article wire:key="customer-dashboard-request-{{ $request->id }}" class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="min-w-0 truncate font-medium text-zinc-900 dark:text-white">{{ $request->title }}</h3>
                        <x-dashboard.status-badge :label="$this->requestStatusLabel($request->status)" :classes="$this->requestStatusClasses($request->status)" />
                    </div>
                    <p class="mt-2 line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $request->description ?: 'Sin descripción adicional.' }}</p>
                    <p class="mt-4 text-xs text-zinc-500 dark:text-zinc-400">Enviada el {{ $request->created_at?->format('d/m/Y') }}</p>
                </article>
            @empty
                <div class="md:col-span-3">
                    <x-dashboard.empty-state title="Todavía no tienes solicitudes" description="Puedes pedir un diseño personalizado desde Solicitudes de diseño." icon="document-text" />
                </div>
            @endforelse
        </div>
    </section>
</div>
