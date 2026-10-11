<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-3">
            <a href="{{ route('admin.sales.index') }}" wire:navigate aria-label="Volver a ventas"
                class="mt-1 inline-flex size-10 shrink-0 items-center justify-center rounded-lg border border-zinc-200 bg-white text-zinc-600 transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"><flux:icon.arrow-left
                    variant="mini" aria-hidden="true" /></a>
            <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Detalle de venta</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-zinc-950 dark:text-white">
                    {{ $order->order_number }}
                </h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Solicitada el
                    {{ optional($order->requested_at)->format('d/m/Y · H:i') }}
                </p>
            </div>
        </div>
        <span
            class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-sm font-medium {{ $this->statusClasses() }}"><span
                class="size-2 rounded-full {{ $this->statusDotClasses() }}"
                aria-hidden="true"></span>{{ $this->statusLabel() }}</span>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Stickers a producir</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums text-zinc-950 dark:text-white">
                {{ number_format($order->items->sum('quantity')) }}
            </p>
            <p class="mt-1 text-xs text-zinc-500">{{ $order->items->count() }}
                {{ $order->items->count() === 1 ? 'diseño' : 'diseños' }} diferentes
            </p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Total de la venta</p>
            <p class="mt-2 text-2xl font-semibold tabular-nums text-zinc-950 dark:text-white">
                ${{ number_format((float) $order->total_amount, 2) }}</p>
            <p class="mt-1 text-xs text-zinc-500">Pagado: ${{ number_format((float) $order->amount_paid, 2) }}</p>
        </div>
        <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Entrega</p>
            <p class="mt-2 text-lg font-semibold text-zinc-950 dark:text-white">
                {{ $order->delivery_method === 'shipping' ? 'Envío' : 'Recoger en tienda' }}
            </p>
            <p class="mt-1 text-xs text-zinc-500">
                {{ $order->estimated_ready_at?->format('d/m/Y') ?? 'Fecha por confirmar' }}
            </p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
        <section
            class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div
                class="flex flex-col gap-2 border-b border-zinc-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-700">
                <div>
                    <h2 class="font-semibold text-zinc-950 dark:text-white">Producción de stickers</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Confirma cantidad, tamaño y acabado de cada
                        diseño.</p>
                </div><span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $order->items->count() }}
                    líneas</span>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($order->items as $item)
                    <article wire:key="order-item-{{ $item->id }}" class="p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex min-w-0 items-start gap-4">
                                <div
                                    class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">
                                    <flux:icon.photo variant="mini" aria-hidden="true" />
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate font-semibold text-zinc-950 dark:text-white">
                                        {{ $item->design_name_snapshot }}
                                    </h3>
                                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Variante
                                        #{{ $item->design_variant_id }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-x-8 gap-y-3 sm:grid-cols-4 lg:min-w-[31rem]">
                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-zinc-500">Tamaño</dt>
                                    <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $item->size_snapshot }}
                                    </dd>
                                    <p class="text-xs text-zinc-500">
                                        {{ number_format((float) $item->width_cm_snapshot, 2) }} ×
                                        {{ number_format((float) $item->height_cm_snapshot, 2) }} cm
                                    </p>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-zinc-500">Acabado</dt>
                                    <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $item->finish_snapshot }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-zinc-500">Cantidad</dt>
                                    <dd class="mt-1 font-semibold tabular-nums text-zinc-900 dark:text-white">
                                        {{ number_format($item->quantity) }}
                                    </dd>
                                    <p class="text-xs text-zinc-500">piezas</p>
                                </div>
                                <div>
                                    <dt class="text-xs uppercase tracking-wide text-zinc-500">Importe</dt>
                                    <dd class="mt-1 font-semibold tabular-nums text-zinc-900 dark:text-white">
                                        ${{ number_format((float) $item->line_total, 2) }}</dd>
                                    <p class="text-xs text-zinc-500">${{ number_format((float) $item->unit_price, 2) }} /
                                        pieza</p>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="p-10 text-center text-sm text-zinc-500">Esta venta aún no tiene stickers configurados.</div>
                @endforelse
            </div>
        </section>

        <aside class="space-y-6">
            <section
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-2">
                    <flux:icon.user variant="mini" class="text-zinc-500" aria-hidden="true" />
                    <h2 class="font-semibold text-zinc-950 dark:text-white">Cliente</h2>
                </div>
                <div class="mt-4 space-y-3 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-zinc-500">Nombre</p>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">
                            {{ $order->customer?->contact_name ?: $order->customer?->user?->name ?: 'Sin nombre' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-zinc-500">Contacto</p>
                        <p class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $order->customer?->email ?: 'Sin correo' }}
                        </p>
                        <p class="text-zinc-700 dark:text-zinc-300">{{ $order->customer?->phone ?: 'Sin teléfono' }}</p>
                    </div>@if ($order->shippingAddress)
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500">Dirección de entrega</p>
                            <p class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $order->shippingAddress->street }}
                                {{ $order->shippingAddress->exterior_number }}, {{ $order->shippingAddress->neighborhood }},
                                {{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} C.P.
                                {{ $order->shippingAddress->postal_code }}
                            </p>
                    </div>@elseif ($order->pickup_location)
                        <div>
                            <p class="text-xs uppercase tracking-wide text-zinc-500">Punto de entrega</p>
                            <p class="mt-1 text-zinc-700 dark:text-zinc-300">{{ $order->pickup_location }}</p>
                    </div>@endif
                </div>
            </section>
            <section
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-2"><flux:icon.document-text variant="mini" class="text-zinc-500"
                        aria-hidden="true" />
                    <h2 class="font-semibold text-zinc-950 dark:text-white">Facturación</h2>
                </div>@if ($order->invoiceRequest)
                    <div
                        class="mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800 dark:bg-emerald-400/10 dark:text-emerald-300">
                        <p class="font-medium">Factura solicitada</p>
                        <p class="mt-1">{{ ucfirst($order->invoiceRequest->status) }}</p>
                    </div>@if ($order->invoiceRequest->taxProfile)
                        <dl class="mt-4 space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <dt class="text-zinc-500">Razón social</dt>
                                <dd class="text-right font-medium text-zinc-900 dark:text-white">
                                    {{ $order->invoiceRequest->taxProfile->legal_name }}
                                </dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-zinc-500">RFC</dt>
                                <dd class="font-medium text-zinc-900 dark:text-white">
                                    {{ $order->invoiceRequest->taxProfile->rfc }}
                                </dd>
                            </div>
                            <div class="flex justify-between gap-3">
                                <dt class="text-zinc-500">C.P. fiscal</dt>
                                <dd class="font-medium text-zinc-900 dark:text-white">
                                    {{ $order->invoiceRequest->taxProfile->fiscal_postal_code }}
                                </dd>
                            </div>
                </dl>@endif @else<p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">El cliente no solicitó factura
                para esta venta.</p>@endif
            </section>
            <section
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="font-semibold text-zinc-950 dark:text-white">Resumen de pago</h2><span
                        class="text-xs font-medium text-zinc-500">{{ $order->payments->count() }} pagos</span>
                </div>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-zinc-500">Subtotal</dt>
                        <dd class="tabular-nums text-zinc-900 dark:text-white">
                            ${{ number_format((float) $order->subtotal, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500">Anticipo requerido</dt>
                        <dd class="tabular-nums text-zinc-900 dark:text-white">
                            ${{ number_format((float) $order->deposit_required, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-zinc-500">Pagado</dt>
                        <dd class="tabular-nums text-zinc-900 dark:text-white">
                            ${{ number_format((float) $order->amount_paid, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-zinc-200 pt-3 font-semibold dark:border-zinc-700">
                        <dt>Saldo pendiente</dt>
                        <dd
                            class="tabular-nums {{ (float) $order->balance_due > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                            ${{ number_format((float) $order->balance_due, 2) }}</dd>
                    </div>
                </dl>
            </section>

            <section
                class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center gap-2">
                    <flux:icon.banknotes variant="mini" class="text-zinc-500" aria-hidden="true" />
                    <h2 class="font-semibold text-zinc-950 dark:text-white">Pagos registrados</h2>
                </div>
                <div class="mt-4 space-y-3">@forelse ($order->payments as $payment)
                    <div wire:key="payment-{{ $payment->id }}"
                        class="rounded-lg border border-zinc-200 p-3 dark:border-zinc-700">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white">
                                    {{ $this->paymentMethodLabel($payment) }}
                                </p>
                                <p class="mt-1 text-xs text-zinc-500">
                                    {{ $payment->paid_at?->format('d/m/Y · H:i') }}{{ $payment->reference ? ' · ' . $payment->reference : '' }}
                                </p>
                            </div><span
                                class="font-semibold tabular-nums text-emerald-600 dark:text-emerald-400">+${{ number_format((float) $payment->amount, 2) }}</span>
                        </div>
                        <p class="mt-2 text-xs text-zinc-500">
                            {{ $this->paymentStatusLabel($payment->status) }}{{ $payment->receivedBy ? ' · ' . $payment->receivedBy->name : '' }}
                        </p>
                </div>@empty<p class="text-sm text-zinc-500 dark:text-zinc-400">Aún no hay pagos registrados.</p>
                    @endforelse
                </div>
            </section>

            <section
                class="rounded-xl border border-dashed border-indigo-200 bg-indigo-50/50 p-5 dark:border-indigo-400/20 dark:bg-indigo-400/10">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-semibold text-zinc-950 dark:text-white">Registrar pago</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Saldo disponible:
                            ${{ number_format((float) $order->balance_due, 2) }}</p>
                    </div><flux:icon.plus-circle variant="mini" class="text-indigo-500" aria-hidden="true" />
                </div><button type="button" wire:click="openPaymentModal" @disabled((float) $order->balance_due <= 0)
                    class="mt-4 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-50">
                    <flux:icon.plus variant="mini" aria-hidden="true" />Agregar pago
                </button>
            </section>
        </aside>
    </div>

    @if ($showPaymentModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-zinc-950/50 p-4" role="dialog"
            aria-modal="true" aria-labelledby="payment-modal-title" wire:keydown.escape="closePaymentModal">
            <div
                class="w-full max-w-lg rounded-2xl border border-zinc-200 bg-white shadow-2xl dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4 border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                    <div>
                        <h2 id="payment-modal-title" class="font-semibold text-zinc-950 dark:text-white">Registrar pago</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Saldo pendiente:
                            ${{ number_format((float) $order->balance_due, 2) }}</p>
                    </div><button type="button" wire:click="closePaymentModal" aria-label="Cerrar ventana"
                        class="inline-flex size-10 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:hover:bg-zinc-800"><flux:icon.x-mark
                            variant="mini" aria-hidden="true" /></button>
                </div>
                <form wire:submit="addPayment" class="space-y-4 p-5">
                    <div><label for="payment-method"
                            class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Método de
                            pago</label><select id="payment-method" wire:model.live="paymentMethod"
                            class="w-full rounded-lg border-zinc-300 bg-white text-sm dark:border-zinc-600 dark:bg-zinc-800">
                            <option value="bank_transfer">Transferencia</option>
                            <option value="cash">Efectivo</option>
                            <option value="other">Otro</option>
                        </select></div>
                    <div><label for="payment-amount"
                            class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Monto</label>
                        <div class="relative"><span
                                class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-zinc-500">$</span><input
                                id="payment-amount" type="number" step="0.01" min="0.01"
                                max="{{ max(0, (float) $order->balance_due) }}" wire:model="paymentAmount"
                                class="w-full rounded-lg border-zinc-300 bg-white pl-7 text-sm dark:border-zinc-600 dark:bg-zinc-800"
                                placeholder="0.00" /> </div>@error('paymentAmount')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>@if ($paymentMethod === 'other')
                        <div><label for="other-method-name"
                                class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">¿Cuál
                                método?</label><input id="other-method-name" type="text" wire:model="otherMethodName"
                                class="w-full rounded-lg border-zinc-300 bg-white text-sm dark:border-zinc-600 dark:bg-zinc-800"
                                placeholder="Ej. Tarjeta" />@error('otherMethodName')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>@endif<div><label for="payment-reference"
                            class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Referencia <span
                                class="font-normal text-zinc-500">(opcional)</span></label><input id="payment-reference"
                            type="text" wire:model="paymentReference"
                            class="w-full rounded-lg border-zinc-300 bg-white text-sm dark:border-zinc-600 dark:bg-zinc-800"
                            placeholder="Folio o referencia bancaria" />@error('paymentReference')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div><label for="payment-note"
                            class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Nota <span
                                class="font-normal text-zinc-500">(opcional)</span></label><textarea id="payment-note"
                            wire:model="paymentNote" rows="2"
                            class="w-full rounded-lg border-zinc-300 bg-white text-sm dark:border-zinc-600 dark:bg-zinc-800"
                            placeholder="Observaciones del pago"></textarea>@error('paymentNote')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end"><button type="button"
                            wire:click="closePaymentModal"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg border border-zinc-300 px-4 text-sm font-medium text-zinc-700 transition hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-800">Cancelar</button><button
                            type="submit" wire:loading.attr="disabled" wire:target="addPayment"
                            class="inline-flex min-h-10 items-center justify-center rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-50"><span
                                wire:loading.remove wire:target="addPayment">Guardar pago</span><span wire:loading
                                wire:target="addPayment">Guardando...</span></button></div>
                </form>
            </div>
        </div>
    @endif
</div>