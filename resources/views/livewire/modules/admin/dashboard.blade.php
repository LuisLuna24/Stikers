<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Panel de administración</p>
            <flux:heading size="xl" class="mt-1">Hola, {{ auth()->user()->name }}</flux:heading>
            <flux:text class="mt-1">Aquí tienes una vista rápida de la operación de Luna Stickers.</flux:text>
        </div>
        <div class="flex flex-wrap gap-3">
            <flux:button href="{{ route('admin.stikers.create') }}" variant="primary" icon="plus" wire:navigate>
                Nuevo sticker
            </flux:button>
            <flux:button href="{{ route('admin.customers.create') }}" variant="ghost" icon="user-plus" wire:navigate>
                Nuevo cliente
            </flux:button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.stat-card label="Pedidos registrados" :value="number_format($stats['orders'])" detail="Histórico total" icon="shopping-cart" />
        <x-dashboard.stat-card label="Pedidos activos" :value="number_format($stats['active_orders'])" detail="Requieren seguimiento" icon="clock" tone="blue" />
        <x-dashboard.stat-card label="Solicitudes pendientes" :value="number_format($stats['pending_requests'])" detail="Diseños por revisar" icon="document-text" tone="amber" />
        <x-dashboard.stat-card label="Cobrado este mes" :value="'$'.number_format((float) $stats['revenue'], 2)" detail="Pagos verificados" icon="banknotes" tone="emerald" />
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-3 border-b border-zinc-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-700">
                <div>
                    <flux:heading size="lg">Pedidos recientes</flux:heading>
                    <flux:text class="mt-1">Revisa los últimos movimientos de venta.</flux:text>
                </div>
                <flux:button href="{{ route('admin.sales.index') }}" variant="ghost" icon="arrow-right" wire:navigate>
                    Ver ventas
                </flux:button>
            </div>

            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($recentOrders as $order)
                    <a wire:key="admin-dashboard-order-{{ $order->id }}" href="{{ route('admin.sales.view', $order->id) }}" wire:navigate class="flex flex-col gap-3 px-5 py-4 transition hover:bg-zinc-50 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-indigo-600 sm:flex-row sm:items-center sm:justify-between dark:hover:bg-zinc-800/60">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">
                                <flux:icon name="shopping-bag" variant="mini" aria-hidden="true" />
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-medium text-zinc-950 dark:text-white">{{ $order->order_number }}</p>
                                <p class="mt-1 truncate text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $order->customer?->contact_name ?: $order->customer?->user?->name ?: 'Cliente sin nombre' }}
                                    · {{ $order->requested_at?->format('d/m/Y') ?: 'Fecha pendiente' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 sm:justify-end">
                            <x-dashboard.status-badge :label="$this->statusLabel($order->status)" :classes="$this->statusClasses($order->status)" :dot="$this->statusDotClasses($order->status)" />
                            <span class="font-semibold tabular-nums text-zinc-950 dark:text-white">${{ number_format((float) $order->total_amount, 2) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="p-5">
                        <x-dashboard.empty-state title="Aún no hay pedidos" description="Cuando se registre una venta aparecerá aquí." icon="shopping-cart" />
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-indigo-100 bg-indigo-50/70 p-5 dark:border-indigo-400/20 dark:bg-indigo-400/10">
                <div class="flex items-start gap-3">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-700 shadow-sm dark:bg-zinc-900 dark:text-indigo-300">
                        <flux:icon name="chart-bar" class="size-5" aria-hidden="true" />
                    </span>
                    <div>
                        <h2 class="font-semibold text-zinc-950 dark:text-white">Catálogo activo</h2>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ number_format($stats['designs']) }} diseños disponibles para tus clientes.</p>
                    </div>
                </div>
                <flux:button href="{{ route('admin.stikers.index') }}" variant="ghost" class="mt-4 w-full" wire:navigate>
                    Administrar catálogo
                </flux:button>
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <flux:heading size="lg">Accesos rápidos</flux:heading>
                <div class="mt-4 grid gap-2">
                    <flux:button href="{{ route('admin.design-requests.index') }}" variant="ghost" icon="document-text" class="justify-start" wire:navigate>Solicitudes de diseño</flux:button>
                    <flux:button href="{{ route('admin.customers.index') }}" variant="ghost" icon="users" class="justify-start" wire:navigate>Clientes</flux:button>
                    <flux:button href="{{ route('admin.catalogs.sizes') }}" variant="ghost" icon="chart-bar" class="justify-start" wire:navigate>Medidas y acabados</flux:button>
                </div>
            </section>
        </aside>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:heading size="lg">Flujo de pedidos</flux:heading>
                    <flux:text class="mt-1">Identifica dónde se concentra la operación.</flux:text>
                </div>
                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $stats['active_orders'] }} activos</span>
            </div>
            <div class="mt-5 space-y-4">
                @foreach ($pipeline as $stage)
                    @php
                        $percentage = $stats['orders'] > 0 ? min(100, (int) round(($stage['count'] / $stats['orders']) * 100)) : 0;
                    @endphp
                    <div wire:key="pipeline-{{ $stage['value'] }}">
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $stage['label'] }}</span>
                            <span class="font-semibold tabular-nums text-zinc-950 dark:text-white">{{ $stage['count'] }}</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800" role="progressbar" aria-label="{{ $stage['label'] }}" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="h-full rounded-full bg-indigo-500 transition-[width] duration-500 dark:bg-indigo-400" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:heading size="lg">Solicitudes de diseño</flux:heading>
                    <flux:text class="mt-1">Últimas solicitudes recibidas.</flux:text>
                </div>
                <flux:button href="{{ route('admin.design-requests.index') }}" variant="ghost" icon="arrow-right" wire:navigate aria-label="Ver todas las solicitudes" />
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($recentRequests as $request)
                    <a wire:key="admin-dashboard-request-{{ $request->id }}" href="{{ route('admin.design-requests.index') }}" wire:navigate class="flex items-start justify-between gap-3 rounded-xl border border-zinc-200 p-3 transition hover:border-indigo-300 hover:bg-indigo-50/40 focus-visible:outline-2 focus-visible:outline-indigo-600 dark:border-zinc-700 dark:hover:border-indigo-400/50 dark:hover:bg-indigo-400/10">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-zinc-900 dark:text-white">{{ $request->title }}</p>
                            <p class="mt-1 truncate text-sm text-zinc-500 dark:text-zinc-400">{{ $request->customer?->contact_name ?: 'Cliente sin nombre' }} · {{ $request->created_at?->format('d/m/Y') }}</p>
                        </div>
                        <x-dashboard.status-badge :label="$this->requestStatusLabel($request->status)" :classes="$this->requestStatusClasses($request->status)" />
                    </a>
                @empty
                    <x-dashboard.empty-state title="No hay solicitudes" description="Las solicitudes personalizadas aparecerán aquí." icon="document-text" />
                @endforelse
            </div>
        </section>
    </div>
</div>
