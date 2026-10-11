<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Relación comercial</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">Clientes</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Consulta sus datos de contacto, pedidos y preferencias de facturación.</p>
        </div>
        <x-w-button href="{{ route('admin.customers.create') }}" icon="plus" primary wire:navigate>
            Nuevo cliente
        </x-w-button>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Clientes registrados</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums text-zinc-950 dark:text-white">{{ number_format($summary['total']) }}</p>
        </div>
        <div class="rounded-xl border border-indigo-200 bg-indigo-50/60 p-5 shadow-sm dark:border-indigo-400/20 dark:bg-indigo-400/10">
            <p class="text-sm text-indigo-700 dark:text-indigo-300">Personas</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums text-indigo-950 dark:text-indigo-100">{{ number_format($summary['individuals']) }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50/60 p-5 shadow-sm dark:border-amber-400/20 dark:bg-amber-400/10">
            <p class="text-sm text-amber-700 dark:text-amber-300">Negocios</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums text-amber-950 dark:text-amber-100">{{ number_format($summary['businesses']) }}</p>
        </div>
    </div>

    <section class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex flex-col gap-4 border-b border-zinc-200 px-5 py-4 lg:flex-row lg:items-end lg:justify-between dark:border-zinc-700">
            <div>
                <h2 class="font-semibold text-zinc-950 dark:text-white">Directorio de clientes</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ number_format($customers->total()) }} resultados encontrados</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <x-w-input wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre, correo o teléfono..." icon="magnifying-glass" aria-label="Buscar clientes" />
                <x-w-select wire:model.live="customerType" :options="$typeOptions" option-label="name" option-value="id" aria-label="Filtrar clientes por tipo" />
            </div>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50/80 dark:bg-zinc-800/60">
                    <tr class="text-left text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        <th scope="col" class="px-5 py-3">Cliente</th>
                        <th scope="col" class="px-5 py-3">Contacto</th>
                        <th scope="col" class="px-5 py-3">Tipo</th>
                        <th scope="col" class="px-5 py-3">Domicilio</th>
                        <th scope="col" class="px-5 py-3">Pedidos</th>
                        <th scope="col" class="px-5 py-3">Acceso</th>
                        <th scope="col" class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($customers as $customer)
                        <tr wire:key="customer-{{ $customer->id }}" class="transition hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-zinc-900 dark:text-white">{{ $customer->contact_name }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Cliente #{{ $customer->id }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-56 truncate text-sm text-zinc-900 dark:text-white">{{ $customer->email }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $customer->phone }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $customer->customer_type === 'business' ? 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' : 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300' }}">
                                    {{ $customer->customer_type === 'business' ? 'Negocio' : 'Persona' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if ($customer->addresses->isNotEmpty())
                                    <p class="max-w-52 truncate text-sm text-zinc-900 dark:text-white">{{ $customer->addresses->first()->city }}</p>
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">CP {{ $customer->addresses->first()->postal_code }}</p>
                                @else
                                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Pendiente</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="font-semibold tabular-nums text-zinc-900 dark:text-white">{{ number_format($customer->orders_count) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 text-sm {{ $customer->user?->status === 'active' ? 'text-emerald-700 dark:text-emerald-300' : 'text-zinc-500 dark:text-zinc-400' }}">
                                    <span class="size-1.5 rounded-full {{ $customer->user?->status === 'active' ? 'bg-emerald-500' : 'bg-zinc-400' }}" aria-hidden="true"></span>
                                    {{ $customer->user?->status === 'active' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" wire:navigate class="inline-flex min-h-10 items-center gap-2 rounded-lg px-3 text-sm font-medium text-indigo-700 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-indigo-300 dark:hover:bg-indigo-400/10">
                                    Editar <flux:icon.arrow-right variant="mini" aria-hidden="true" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-14 text-center">
                                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                    <flux:icon.users variant="mini" aria-hidden="true" />
                                </div>
                                <p class="mt-4 font-medium text-zinc-900 dark:text-white">No hay clientes que coincidan</p>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Prueba con otro criterio o registra un cliente nuevo.</p>
                                <x-w-button class="mt-4" href="{{ route('admin.customers.create') }}" icon="plus" wire:navigate>Registrar cliente</x-w-button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-zinc-200 md:hidden dark:divide-zinc-700">
            @forelse ($customers as $customer)
                <article wire:key="mobile-customer-{{ $customer->id }}" class="space-y-4 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-zinc-900 dark:text-white">{{ $customer->contact_name }}</p>
                            <p class="mt-1 truncate text-xs text-zinc-500 dark:text-zinc-400">{{ $customer->email }}</p>
                        </div>
                        <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-medium {{ $customer->customer_type === 'business' ? 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' : 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300' }}">
                            {{ $customer->customer_type === 'business' ? 'Negocio' : 'Persona' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Teléfono</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $customer->phone }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Pedidos</p>
                            <p class="mt-1 font-semibold tabular-nums text-zinc-900 dark:text-white">{{ number_format($customer->orders_count) }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Domicilio</p>
                            <p class="mt-1 truncate font-medium text-zinc-900 dark:text-white">{{ $customer->addresses->first()?->city ?? 'Pendiente' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Acceso</p>
                            <p class="mt-1 font-medium {{ $customer->user?->status === 'active' ? 'text-emerald-700 dark:text-emerald-300' : 'text-zinc-500 dark:text-zinc-400' }}">{{ $customer->user?->status === 'active' ? 'Activo' : 'Inactivo' }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" wire:navigate class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-zinc-200 text-sm font-medium text-indigo-700 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:text-indigo-300 dark:hover:bg-indigo-400/10">
                        Editar cliente <flux:icon.arrow-right variant="mini" aria-hidden="true" />
                    </a>
                </article>
            @empty
                <div class="p-12 text-center text-sm text-zinc-500 dark:text-zinc-400">No hay clientes que coincidan con los filtros.</div>
            @endforelse
        </div>

        @if ($customers->hasPages())
            <div class="border-t border-zinc-200 px-5 py-4 dark:border-zinc-700">{{ $customers->links() }}</div>
        @endif
    </section>
</div>
