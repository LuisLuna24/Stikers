<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Seguimiento</p>
            <flux:heading size="xl">Mis pedidos</flux:heading>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Consulta el estado de tus órdenes y envía tus comprobantes de transferencia.</p>
        </div>
        <flux:button href="{{ route('customer.purchase.create') }}" variant="primary" icon="plus" wire:navigate>
            Crear pedido
        </flux:button>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <flux:card>
            <flux:text>Pedidos registrados</flux:text>
            <flux:heading size="2xl" class="mt-2 tabular-nums">{{ number_format($summary['total']) }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Pedidos en proceso</flux:text>
            <flux:heading size="2xl" class="mt-2 tabular-nums">{{ number_format($summary['active']) }}</flux:heading>
        </flux:card>
        <flux:card>
            <flux:text>Anticipos pendientes</flux:text>
            <flux:heading size="2xl" class="mt-2 tabular-nums">{{ number_format($summary['pending']) }}</flux:heading>
        </flux:card>
    </div>

    <flux:card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <flux:heading size="lg">Historial de pedidos</flux:heading>
                <flux:text class="mt-1">{{ number_format($orders->total()) }} resultados encontrados</flux:text>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Buscar por número de pedido..." icon="magnifying-glass" aria-label="Buscar pedidos" />
                <flux:select wire:model.live="status" aria-label="Filtrar pedidos por estado">
                    <option value="">Todos los estados</option>
                    @foreach ($statusOptions as $statusOption)
                        <option value="{{ $statusOption['value'] }}">{{ $statusOption['label'] }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        <div class="mt-5 space-y-3 md:hidden">
            @forelse ($orders as $order)
                <a wire:key="customer-order-mobile-{{ $order->id }}"
                    href="{{ route('customer.purchase.show', $order->id) }}"
                    wire:navigate
                    class="block rounded-xl border border-zinc-200 bg-white p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-indigo-400/60 dark:hover:bg-indigo-500/5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-zinc-950 dark:text-white">{{ $order->order_number }}</p>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $order->requested_at?->format('d/m/Y · H:i') }}</p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium {{ $this->statusClasses($order->status) }}">
                            <span class="size-1.5 rounded-full {{ $this->statusDotClasses($order->status) }}" aria-hidden="true"></span>
                            {{ $this->statusLabel($order->status) }}
                        </span>
                    </div>

                    <dl class="mt-4 grid grid-cols-2 gap-3 border-t border-zinc-200 pt-3 text-sm dark:border-zinc-700">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Artículos</dt>
                            <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ number_format((int) $order->items_count) }} {{ $order->items_count === 1 ? 'línea' : 'líneas' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Total</dt>
                            <dd class="mt-1 font-medium tabular-nums text-zinc-900 dark:text-white">${{ number_format((float) $order->total_amount, 2) }}</dd>
                        </div>
                    </dl>

                    <div class="mt-4 flex items-center justify-between gap-3 border-t border-zinc-200 pt-3 dark:border-zinc-700">
                        <span class="text-sm font-medium {{ (float) $order->balance_due > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            {{ (float) $order->balance_due > 0 ? 'Saldo: $'.number_format((float) $order->balance_due, 2) : 'Liquidado' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                            Ver detalle
                            <flux:icon.arrow-right variant="mini" aria-hidden="true" />
                        </span>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-zinc-200 px-5 py-10 text-center dark:border-zinc-700">
                    <flux:icon name="inbox" class="mx-auto size-8 text-zinc-400" aria-hidden="true" />
                    <flux:text class="mt-2 font-medium">No hay pedidos que coincidan</flux:text>
                    <flux:text>Prueba con otro filtro o crea un pedido nuevo.</flux:text>
                </div>
            @endforelse

            @if ($orders->hasPages())
                <div class="pt-2">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

        <div class="mt-5 hidden overflow-x-auto md:block">
            <flux:table :paginate="$orders">
                <flux:table.columns sticky>
                    <flux:table.column>Pedido</flux:table.column>
                    <flux:table.column>Artículos</flux:table.column>
                    <flux:table.column>Estatus</flux:table.column>
                    <flux:table.column>Pago</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($orders as $order)
                        <flux:table.row :key="$order->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">
                                        <flux:icon name="shopping-bag" variant="mini" aria-hidden="true" />
                                    </span>
                                    <div>
                                        <p class="font-medium">{{ $order->order_number }}</p>
                                        <p class="mt-1 text-xs text-zinc-500">{{ $order->requested_at?->format('d/m/Y · H:i') }}</p>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                <span class="font-medium tabular-nums">{{ number_format((int) $order->items_count) }}</span>
                                <span class="text-sm text-zinc-500">{{ $order->items_count === 1 ? 'línea' : 'líneas' }}</span>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                <span class="inline-flex items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium {{ $this->statusClasses($order->status) }}">
                                    <span class="size-1.5 rounded-full {{ $this->statusDotClasses($order->status) }}" aria-hidden="true"></span>
                                    {{ $this->statusLabel($order->status) }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                <p class="font-medium tabular-nums">${{ number_format((float) $order->amount_paid, 2) }} / ${{ number_format((float) $order->total_amount, 2) }}</p>
                                <p class="mt-1 text-xs {{ (float) $order->balance_due > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ (float) $order->balance_due > 0 ? 'Saldo: $'.number_format((float) $order->balance_due, 2) : 'Liquidado' }}
                                </p>
                            </flux:table.cell>
                            <flux:table.cell class="py-0 text-right">
                                <flux:button href="{{ route('customer.purchase.show', $order->id) }}" variant="ghost" size="sm" icon="eye" wire:navigate>
                                    Ver detalle
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon name="inbox" class="size-8 text-zinc-400" aria-hidden="true" />
                                    <flux:text class="font-medium">No hay pedidos que coincidan</flux:text>
                                    <flux:text>Prueba con otro filtro o crea un pedido nuevo.</flux:text>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>

    <flux:modal name="customer-order-details" class="w-full max-w-5xl" scroll="body">
        @if ($selectedOrder)
            @php
                $currentFlowIndex = collect($this->statusFlow())->search(fn ($flowStatus) => $flowStatus->value === $selectedOrder->status);
                $availableBalance = $this->availablePaymentBalance($selectedOrder);
            @endphp

            <div class="space-y-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <flux:text class="text-indigo-600 dark:text-indigo-400">Detalle del pedido</flux:text>
                        <flux:heading size="xl" class="mt-1">{{ $selectedOrder->order_number }}</flux:heading>
                        <flux:text class="mt-1">Solicitado el {{ $selectedOrder->requested_at?->format('d/m/Y · H:i') }}</flux:text>
                    </div>
                    <span class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium {{ $this->statusClasses($selectedOrder->status) }}">
                        <span class="size-2 rounded-full {{ $this->statusDotClasses($selectedOrder->status) }}" aria-hidden="true"></span>
                        {{ $this->statusLabel($selectedOrder->status) }}
                    </span>
                </div>

                <flux:card>
                    <flux:heading size="lg">Flujo del pedido</flux:heading>
                    <div class="mt-5 grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach ($this->statusFlow() as $flowIndex => $flowStatus)
                            @php
                                $isCurrent = $selectedOrder->status === $flowStatus->value;
                                $isCompleted = $currentFlowIndex !== false && $flowIndex < $currentFlowIndex;
                            @endphp
                            <div wire:key="flow-{{ $selectedOrder->id }}-{{ $flowStatus->value }}" class="relative flex gap-3 sm:block">
                                @if ($flowIndex > 0)
                                    <span class="absolute -top-3 left-3 hidden h-px w-[calc(100%-1.5rem)] bg-zinc-200 dark:bg-zinc-700 sm:block" aria-hidden="true"></span>
                                @endif
                                <span class="relative flex size-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold {{ $isCurrent ? 'bg-indigo-600 text-white ring-4 ring-indigo-100 dark:ring-indigo-400/20' : ($isCompleted ? 'bg-emerald-500 text-white' : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400') }}">
                                    @if ($isCompleted)
                                        <flux:icon name="check" variant="mini" aria-hidden="true" />
                                    @else
                                        {{ $flowIndex + 1 }}
                                    @endif
                                </span>
                                <div>
                                    <p class="mt-1 text-sm font-medium {{ $isCurrent ? 'text-indigo-700 dark:text-indigo-300' : 'text-zinc-700 dark:text-zinc-300' }}">{{ $flowStatus->label() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if ($currentFlowIndex === false)
                        <flux:callout class="mt-5" variant="warning" icon="exclamation-triangle" heading="Estado especial">
                            Este pedido tiene el estado {{ $this->statusLabel($selectedOrder->status) }} y no continúa por el flujo normal.
                        </flux:callout>
                    @endif
                </flux:card>

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
                    <flux:card>
                        <flux:heading size="lg">Artículos del pedido</flux:heading>
                        <div class="mt-4 divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse ($selectedOrder->items as $item)
                                <div wire:key="customer-order-item-{{ $item->id }}" class="flex flex-col gap-3 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-medium">{{ $item->design_name_snapshot }}</p>
                                        <p class="mt-1 text-sm text-zinc-500">{{ $item->size_snapshot }} · {{ $item->finish_snapshot }} · {{ number_format($item->quantity) }} piezas</p>
                                    </div>
                                    <p class="font-medium tabular-nums">${{ number_format((float) $item->line_total, 2) }}</p>
                                </div>
                            @empty
                                <flux:text class="py-6">Este pedido aún no tiene artículos.</flux:text>
                            @endforelse
                        </div>
                    </flux:card>

                    <flux:card>
                        <flux:heading size="lg">Resumen de pago</flux:heading>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-zinc-500">Total</dt><dd class="font-medium tabular-nums">${{ number_format((float) $selectedOrder->total_amount, 2) }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-zinc-500">Pagado</dt><dd class="font-medium tabular-nums">${{ number_format((float) $selectedOrder->amount_paid, 2) }}</dd></div>
                            <div class="flex justify-between gap-3 border-t border-zinc-200 pt-3 dark:border-zinc-700"><dt class="font-medium">Saldo</dt><dd class="font-semibold tabular-nums {{ (float) $selectedOrder->balance_due > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">${{ number_format((float) $selectedOrder->balance_due, 2) }}</dd></div>
                        </dl>
                    </flux:card>
                </div>

                <flux:card>
                    <div class="flex items-center justify-between gap-3">
                        <flux:heading size="lg">Pagos enviados</flux:heading>
                        <flux:badge>{{ $selectedOrder->payments->count() }}</flux:badge>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse ($selectedOrder->payments as $payment)
                            <div wire:key="customer-payment-{{ $payment->id }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="font-medium">{{ $this->paymentMethodLabel($payment) }}</p>
                                        <p class="mt-1 text-xs text-zinc-500">{{ $payment->paid_at?->format('d/m/Y · H:i') }}{{ $payment->reference ? ' · Referencia: '.$payment->reference : '' }}</p>
                                    </div>
                                    <p class="font-semibold tabular-nums">${{ number_format((float) $payment->amount, 2) }}</p>
                                </div>
                                <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                                    <span class="font-medium {{ $this->paymentStatusClasses($payment->status) }}">{{ $this->paymentStatusLabel($payment->status) }}</span>
                                    @if ($proofUrl = $this->proofUrl($payment->proof_url))
                                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline dark:text-indigo-400">Ver comprobante</a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <flux:text>Aún no has enviado pagos para este pedido.</flux:text>
                        @endforelse
                    </div>
                </flux:card>

                @if ($availableBalance > 0 && ! in_array($selectedOrder->status, [\App\Enums\OrderStatus::Cancelled->value, \App\Enums\OrderStatus::Refunded->value], true))
                    <flux:card class="border-indigo-200 dark:border-indigo-400/30">
                        <flux:heading size="lg">Enviar comprobante de transferencia</flux:heading>
                        <flux:text class="mt-1">Saldo disponible para registrar: ${{ number_format($availableBalance, 2) }}. El pago se aplicará cuando el equipo lo verifique.</flux:text>

                        <form wire:submit="submitPaymentProof" class="mt-5 space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <flux:input wire:model="paymentAmount" label="Monto transferido" type="number" min="0.01" max="{{ $availableBalance }}" step="0.01" placeholder="Ej. 150.00" />
                                <flux:input wire:model="paymentReference" label="Referencia" description="Opcional" placeholder="Ej. 123456789" />
                            </div>
                            <flux:input wire:model="paymentProof" label="Comprobante" description="JPG, PNG o PDF · máximo 5 MB" type="file" accept=".jpg,.jpeg,.png,.pdf" />
                            @if ($paymentProof)
                                <flux:callout variant="info" icon="document-check">Comprobante listo para enviar: {{ $paymentProof->getClientOriginalName() }}</flux:callout>
                            @endif
                            <div class="flex justify-end">
                                <flux:button type="submit" variant="primary" icon="arrow-up-tray" wire:loading.attr="disabled" wire:target="submitPaymentProof,paymentProof">
                                    Enviar comprobante
                                </flux:button>
                            </div>
                        </form>
                    </flux:card>
                @endif

                <div class="flex justify-end">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cerrar</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
