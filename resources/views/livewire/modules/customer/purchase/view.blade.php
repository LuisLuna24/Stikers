<div class="space-y-6">
    @php
        $currentFlowIndex = collect($this->statusFlow())->search(fn ($flowStatus) => $flowStatus->value === $order->status);
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-3">
            <flux:button href="{{ route('customer.purchase.index') }}" variant="ghost" icon="arrow-left" wire:navigate aria-label="Volver a mis pedidos" />
            <div>
                <flux:text class="text-indigo-600 dark:text-indigo-400">Detalle del pedido</flux:text>
                <flux:heading size="xl" class="mt-1">{{ $order->order_number }}</flux:heading>
                <flux:text class="mt-1">Solicitado el {{ $order->requested_at?->format('d/m/Y · H:i') }}</flux:text>
            </div>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium {{ $this->statusClasses($order->status) }}">
            <span class="size-2 rounded-full {{ $this->statusDotClasses($order->status) }}" aria-hidden="true"></span>
            {{ $this->statusLabel($order->status) }}
        </span>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <flux:card>
            <flux:text>Stickers de la orden</flux:text>
            <flux:heading size="2xl" class="mt-2 tabular-nums">{{ number_format((int) $order->items->sum('quantity')) }}</flux:heading>
            <flux:text class="mt-1">{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'línea' : 'líneas' }}</flux:text>
        </flux:card>
        <flux:card>
            <flux:text>Total de la orden</flux:text>
            <flux:heading size="2xl" class="mt-2 tabular-nums">${{ number_format((float) $order->total_amount, 2) }}</flux:heading>
            <flux:text class="mt-1">Pagado: ${{ number_format((float) $order->amount_paid, 2) }}</flux:text>
        </flux:card>
        <flux:card>
            <flux:text>Saldo pendiente</flux:text>
            <flux:heading size="2xl" class="mt-2 tabular-nums {{ (float) $order->balance_due > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">${{ number_format((float) $order->balance_due, 2) }}</flux:heading>
            <flux:text class="mt-1">{{ $order->delivery_method === 'shipping' ? 'Envío' : 'Recoger en tienda' }}</flux:text>
        </flux:card>
    </div>

    <flux:card>
        <flux:heading size="lg">Flujo de la orden</flux:heading>
        <div class="mt-5 grid gap-4 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($this->statusFlow() as $flowIndex => $flowStatus)
                @php
                    $isCurrent = $order->status === $flowStatus->value;
                    $isCompleted = $currentFlowIndex !== false && $flowIndex < $currentFlowIndex;
                @endphp
                <div wire:key="order-flow-{{ $order->id }}-{{ $flowStatus->value }}" class="relative flex gap-3 sm:block">
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
                    <p class="mt-1 text-sm font-medium {{ $isCurrent ? 'text-indigo-700 dark:text-indigo-300' : 'text-zinc-700 dark:text-zinc-300' }}">{{ $flowStatus->label() }}</p>
                </div>
            @endforeach
        </div>
        @if ($currentFlowIndex === false)
            <flux:callout class="mt-5" variant="warning" icon="exclamation-triangle" heading="Estado especial">
                Esta orden tiene el estado {{ $this->statusLabel($order->status) }} y no continúa por el flujo normal.
            </flux:callout>
        @endif
    </flux:card>

    <flux:card>
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="lg">Stickers de esta orden</flux:heading>
                <flux:text class="mt-1">Estos son exclusivamente los diseños y variantes incluidos en {{ $order->order_number }}.</flux:text>
            </div>
            <flux:badge>{{ $order->items->count() }} {{ $order->items->count() === 1 ? 'línea' : 'líneas' }}</flux:badge>
        </div>

        <div class="mt-5 overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Sticker</flux:table.column>
                    <flux:table.column>Tamaño</flux:table.column>
                    <flux:table.column>Acabado</flux:table.column>
                    <flux:table.column>Cantidad</flux:table.column>
                    <flux:table.column>Precio unitario</flux:table.column>
                    <flux:table.column>Importe</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($order->items as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">
                                        <flux:icon name="photo" variant="mini" aria-hidden="true" />
                                    </span>
                                    <div>
                                        <p class="font-medium">{{ $item->design_name_snapshot }}</p>
                                        <p class="mt-1 text-xs text-zinc-500">Variante #{{ $item->design_variant_id }}</p>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                <p>{{ $item->size_snapshot }}</p>
                                <p class="mt-1 text-xs text-zinc-500">{{ number_format((float) $item->width_cm_snapshot, 2) }} × {{ number_format((float) $item->height_cm_snapshot, 2) }} cm</p>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">{{ $item->finish_snapshot }}</flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap font-semibold tabular-nums">{{ number_format($item->quantity) }}</flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap tabular-nums">${{ number_format((float) $item->unit_price, 2) }}</flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap font-semibold tabular-nums">${{ number_format((float) $item->line_total, 2) }}</flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="py-10 text-center">Esta orden no tiene stickers registrados.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>

    <div>
        <flux:card>
            <div class="flex items-center justify-between gap-3">
                <flux:heading size="lg">Pagos</flux:heading>
                <flux:badge>{{ $order->payments->count() }}</flux:badge>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($order->payments as $payment)
                    <div wire:key="order-payment-{{ $payment->id }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium">{{ $this->paymentMethodLabel($payment) }}</p>
                                <p class="mt-1 text-xs text-zinc-500">{{ $payment->paid_at?->format('d/m/Y · H:i') }}{{ $payment->reference ? ' · Referencia: '.$payment->reference : '' }}</p>
                            </div>
                            <p class="font-semibold tabular-nums">${{ number_format((float) $payment->amount, 2) }}</p>
                        </div>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs">
                            <span class="font-medium {{ $this->paymentStatusClasses($payment->status) }}">{{ $this->paymentStatusLabel($payment->status) }}</span>
                            @if ($proofUrl = $this->paymentProofUrl($payment->proof_url))
                                <a href="{{ $proofUrl }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline dark:text-indigo-400">Ver comprobante</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <flux:text>Aún no hay pagos registrados para esta orden.</flux:text>
                @endforelse
            </div>
        </flux:card>
    </div>

    <div class="flex justify-end">
        <flux:button href="{{ route('customer.purchase.index') }}" variant="ghost" wire:navigate>Volver a mis pedidos</flux:button>
    </div>
</div>
