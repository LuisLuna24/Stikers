<div
    x-data="{ drawerOpen: false }"
    x-on:keydown.escape.window="drawerOpen = false"
    class="min-h-dvh bg-zinc-50 text-zinc-950 dark:bg-zinc-950 dark:text-white"
>
    <div class="mx-auto max-w-7xl space-y-6 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
        <header class="flex flex-col gap-5 border-b border-zinc-200 pb-6 dark:border-zinc-800 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Cotizador</p>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Arma tu pedido</h1>
                <p class="mt-2 max-w-xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                    Elige un diseño, ajusta su variante y agrega la cantidad que necesitas. Verás el precio actualizado en cada paso.
                </p>
            </div>

            <button
                type="button"
                x-on:click="drawerOpen = true"
                class="relative inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 text-sm font-semibold text-zinc-900 transition hover:border-indigo-400 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:hover:border-indigo-400 dark:hover:text-indigo-300 dark:focus:ring-offset-zinc-950"
                aria-label="Abrir carrito"
            >
                <flux:icon.shopping-bag class="size-5" aria-hidden="true" />
                <span>Carrito</span>
                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-indigo-600 px-1.5 py-0.5 text-xs font-bold tabular-nums text-white">
                    {{ $cartItemCount }}
                </span>
            </button>
        </header>

        @error('cart')
            <div role="alert" class="flex items-start gap-3 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-400/30 dark:bg-rose-400/10 dark:text-rose-200">
                <flux:icon.exclamation-triangle class="mt-0.5 size-5 shrink-0" aria-hidden="true" />
                <span>{{ $message }}</span>
            </div>
        @enderror

        <section aria-labelledby="catalog-heading">
            <div class="mb-4 flex items-end justify-between gap-3">
                <div>
                    <h2 id="catalog-heading" class="text-lg font-semibold tracking-tight">Catálogo disponible</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Precios por pieza, antes de envío.</p>
                </div>
                <p class="text-sm tabular-nums text-zinc-500 dark:text-zinc-400">{{ $designs->count() }} {{ $designs->count() === 1 ? 'diseño' : 'diseños' }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($designs as $design)
                    @php
                        $data = $catalogData[$design->id];
                        $firstVariant = $data['variants'][0];
                        $previewUrl = $this->previewUrl($design->preview_image_url);
                    @endphp

                    <article
                        wire:key="design-{{ $design->id }}"
                        x-data="{
                            catalog: @js($data),
                            selectedSizeId: @js($firstVariant['size_id']),
                            selectedFinishId: @js($firstVariant['finish_id']),
                            quantity: @js($firstVariant['minimum_quantity']),
                            formatPrice(value) {
                                return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN', maximumFractionDigits: 2 }).format(value || 0)
                            },
                            selectedVariant() {
                                return this.catalog.variants.find(variant => Number(variant.size_id) === Number(this.selectedSizeId) && Number(variant.finish_id) === Number(this.selectedFinishId)) || this.catalog.variants[0]
                            },
                            selectedSize() {
                                return this.catalog.sizes.find(size => Number(size.id) === Number(this.selectedSizeId)) || this.catalog.sizes[0]
                            },
                            selectedFinish() {
                                return this.catalog.finishes.find(finish => Number(finish.id) === Number(this.selectedFinishId)) || this.catalog.finishes[0]
                            },
                            quantityIsValid() {
                                const variant = this.selectedVariant()
                                const value = Number(this.quantity)
                                return variant && value >= Number(variant.minimum_quantity) && (value - Number(variant.minimum_quantity)) % Number(variant.quantity_increment) === 0
                            },
                            syncQuantity() {
                                const variant = this.selectedVariant()
                                if (variant && Number(this.quantity) < Number(variant.minimum_quantity)) this.quantity = Number(variant.minimum_quantity)
                            },
                            sizePrice(sizeId) {
                                const variant = this.catalog.variants.find(item => Number(item.size_id) === Number(sizeId) && Number(item.finish_id) === Number(this.selectedFinishId))
                                return variant ? this.formatPrice(variant.price) : ''
                            },
                            finishPrice(finishId) {
                                const variant = this.catalog.variants.find(item => Number(item.finish_id) === Number(finishId) && Number(item.size_id) === Number(this.selectedSizeId))
                                return variant ? this.formatPrice(variant.price) : ''
                            }
                        }"
                        class="flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white transition duration-200 hover:-translate-y-0.5 hover:border-indigo-300 dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-indigo-500/60"
                    >
                        <div class="relative aspect-[4/3] overflow-hidden border-b border-zinc-200 bg-zinc-100 dark:border-zinc-800 dark:bg-zinc-800">
                            @if ($previewUrl)
                                <img src="{{ $previewUrl }}" alt="Vista previa de {{ $design->name }}" loading="lazy" width="800" height="600" class="size-full object-cover transition duration-300" />
                            @else
                                <div class="flex size-full items-center justify-center text-zinc-400 dark:text-zinc-500">
                                    <flux:icon.photo class="size-12" aria-hidden="true" />
                                    <span class="sr-only">Sin vista previa disponible</span>
                                </div>
                            @endif
                            <span class="absolute left-3 top-3 rounded-md border border-white/70 bg-white/90 px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-zinc-700 backdrop-blur dark:border-zinc-700/80 dark:bg-zinc-900/90 dark:text-zinc-200">{{ $design->sku }}</span>
                        </div>

                        <div class="flex flex-1 flex-col gap-4 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="text-base font-semibold text-zinc-950 dark:text-white">{{ $design->name }}</h3>
                                    <p class="mt-1 line-clamp-2 text-sm leading-5 text-zinc-500 dark:text-zinc-400">{{ $design->description ?: 'Diseño listo para personalizar.' }}</p>
                                </div>
                                <div class="shrink-0 text-right">
                                    <p class="text-[11px] font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Desde</p>
                                    <p class="mt-0.5 text-lg font-semibold tabular-nums text-zinc-950 dark:text-white" x-text="formatPrice(Math.min(...catalog.variants.map(variant => variant.price)))"></p>
                                </div>
                            </div>

                            <div class="grid gap-3">
                                <div>
                                    <label for="size-{{ $design->id }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">Tamaño</label>
                                    <select id="size-{{ $design->id }}" x-model="selectedSizeId" x-on:change="syncQuantity()" class="min-h-11 w-full rounded-lg border-zinc-300 bg-white px-3 text-sm text-zinc-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                                        <template x-for="size in catalog.sizes" :key="size.id">
                                            <option :value="size.id" x-text="`${size.name} · ${size.dimensions} · ${sizePrice(size.id)}`"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label for="finish-{{ $design->id }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">Acabado</label>
                                    <select id="finish-{{ $design->id }}" x-model="selectedFinishId" x-on:change="syncQuantity()" class="min-h-11 w-full rounded-lg border-zinc-300 bg-white px-3 text-sm text-zinc-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                                        <template x-for="finish in catalog.finishes" :key="finish.id">
                                            <option :value="finish.id" x-text="`${finish.name} · ${finishPrice(finish.id)}`"></option>
                                        </template>
                                    </select>
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400" x-text="selectedFinish()?.description || 'Acabado seleccionado para tu pedido.'"></p>
                                </div>
                            </div>

                            <div class="rounded-xl border border-indigo-100 bg-indigo-50/70 p-3 dark:border-indigo-400/20 dark:bg-indigo-400/10">
                                <div class="flex items-end justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-700 dark:text-indigo-300">Precio por pieza</p>
                                        <p class="mt-1 text-2xl font-semibold tabular-nums text-indigo-950 dark:text-indigo-100" x-text="formatPrice(selectedVariant()?.price)"></p>
                                    </div>
                                    <div class="text-right text-xs text-indigo-800 dark:text-indigo-200">
                                        <p x-text="`${selectedSize()?.name || ''} · ${selectedFinish()?.name || ''}`"></p>
                                        <p class="mt-1 tabular-nums" x-text="`Total: ${formatPrice((selectedVariant()?.price || 0) * (Number(quantity) || 0))}`"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-auto grid gap-3 sm:grid-cols-[8rem_minmax(0,1fr)] sm:items-end">
                                <div>
                                    <label for="quantity-{{ $design->id }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-zinc-600 dark:text-zinc-400">Cantidad</label>
                                    <input id="quantity-{{ $design->id }}" type="number" inputmode="numeric" min="5" step="5" x-model.number="quantity" x-bind:min="selectedVariant()?.minimum_quantity" x-bind:step="selectedVariant()?.quantity_increment" aria-describedby="quantity-help-{{ $design->id }}" class="min-h-11 w-full rounded-lg border-zinc-300 bg-white px-3 text-sm tabular-nums text-zinc-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                                    <p id="quantity-help-{{ $design->id }}" class="mt-1 text-xs text-zinc-500 dark:text-zinc-400" x-show="selectedVariant()" x-text="`Mínimo ${selectedVariant()?.minimum_quantity} · múltiplos de ${selectedVariant()?.quantity_increment}`"></p>
                                    <p class="mt-1 text-xs font-medium text-rose-700 dark:text-rose-300" x-show="quantity !== '' && !quantityIsValid()" x-text="`Usa al menos ${selectedVariant()?.minimum_quantity} y múltiplos de ${selectedVariant()?.quantity_increment}.`" role="alert"></p>
                                </div>
                                <button type="button" x-on:click="$wire.addToCart(Number(selectedVariant().id), Number(quantity))" x-bind:disabled="!quantityIsValid()" wire:loading.attr="disabled" wire:target="addToCart" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-zinc-900">
                                    <flux:icon.plus class="size-4" aria-hidden="true" />
                                    <span>Agregar al carrito</span>
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-zinc-300 bg-white px-6 py-14 text-center dark:border-zinc-700 dark:bg-zinc-900">
                        <flux:icon.rectangle-stack class="mx-auto size-10 text-zinc-400" aria-hidden="true" />
                        <h3 class="mt-4 font-semibold">El catálogo está en preparación</h3>
                        <p class="mx-auto mt-1 max-w-md text-sm text-zinc-500 dark:text-zinc-400">En cuanto haya diseños disponibles podrás agregarlos a tu cotización desde aquí.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <div x-cloak x-show="drawerOpen" class="fixed inset-0 z-50" role="presentation">
        <button type="button" x-on:click="drawerOpen = false" class="absolute inset-0 bg-zinc-950/40 backdrop-blur-[1px]" aria-label="Cerrar carrito"></button>
        <aside x-show="drawerOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col border-l border-zinc-200 bg-white shadow-2xl dark:border-zinc-800 dark:bg-zinc-900" role="dialog" aria-modal="true" aria-labelledby="cart-heading">
            <div class="flex items-start justify-between gap-4 border-b border-zinc-200 px-5 py-5 dark:border-zinc-800">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600 dark:text-indigo-400">Resumen</p>
                    <h2 id="cart-heading" class="mt-1 text-xl font-semibold tracking-tight">Tu carrito</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $cartItemCount }} {{ $cartItemCount === 1 ? 'línea' : 'líneas' }} seleccionadas</p>
                </div>
                <button type="button" x-on:click="drawerOpen = false" class="inline-flex size-11 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:hover:bg-zinc-800 dark:hover:text-white" aria-label="Cerrar carrito">
                    <flux:icon.x-mark class="size-5" aria-hidden="true" />
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
                @forelse ($cartItems as $item)
                    @php $variant = $item['variant']; @endphp
                    <article wire:key="cart-{{ $variant->id }}" class="flex gap-3 border-b border-zinc-200 py-4 first:pt-0 last:border-b-0 dark:border-zinc-800">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">
                            <flux:icon.photo class="size-5" aria-hidden="true" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate text-sm font-semibold text-zinc-950 dark:text-white">{{ $variant->design->name }}</h3>
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $variant->stickerSize->name }} · {{ $variant->stickerFinish->name }}</p>
                                </div>
                                <button type="button" wire:click="removeFromCart({{ $variant->id }})" wire:loading.attr="disabled" wire:target="removeFromCart({{ $variant->id }})" class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg text-zinc-400 transition hover:bg-rose-50 hover:text-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 dark:hover:bg-rose-400/10 dark:hover:text-rose-300" aria-label="Eliminar {{ $variant->design->name }} del carrito">
                                    <flux:icon.trash class="size-4" aria-hidden="true" />
                                </button>
                            </div>
                            <div class="mt-3 flex items-end justify-between gap-3">
                                <div>
                                    <label for="cart-quantity-{{ $variant->id }}" class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Cantidad</label>
                                    <input id="cart-quantity-{{ $variant->id }}" type="number" min="{{ $variant->minimum_quantity }}" step="{{ $variant->quantity_increment }}" value="{{ $item['quantity'] }}" wire:change="updateCartQuantity({{ $variant->id }}, $event.target.value)" class="mt-1 min-h-10 w-24 rounded-lg border-zinc-300 bg-white px-2.5 text-sm tabular-nums text-zinc-900 focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" aria-describedby="cart-help-{{ $variant->id }}" />
                                    <p id="cart-help-{{ $variant->id }}" class="mt-1 text-[11px] text-zinc-500 dark:text-zinc-400">Mín. {{ $variant->minimum_quantity }}</p>
                                </div>
                                <p class="text-right text-sm font-semibold tabular-nums text-zinc-950 dark:text-white">${{ number_format($item['subtotal'], 2) }}<span class="mt-1 block text-xs font-normal text-zinc-500">${{ number_format((float) $variant->unit_price, 2) }} / pieza</span></p>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="flex min-h-64 flex-col items-center justify-center text-center">
                        <div class="flex size-14 items-center justify-center rounded-full border border-dashed border-zinc-300 text-zinc-400 dark:border-zinc-700 dark:text-zinc-500">
                            <flux:icon.shopping-bag class="size-6" aria-hidden="true" />
                        </div>
                        <h3 class="mt-4 font-semibold">Tu carrito está vacío</h3>
                        <p class="mt-1 max-w-xs text-sm leading-6 text-zinc-500 dark:text-zinc-400">Selecciona un diseño y configura su tamaño, acabado y cantidad para empezar.</p>
                        <button type="button" x-on:click="drawerOpen = false" class="mt-5 inline-flex min-h-11 items-center rounded-lg px-4 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-indigo-300 dark:hover:bg-indigo-400/10">Explorar catálogo</button>
                    </div>
                @endforelse
            </div>

            <div class="space-y-3 border-t border-zinc-200 bg-zinc-50 px-5 py-5 dark:border-zinc-800 dark:bg-zinc-950">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Total estimado</span>
                    <span class="text-2xl font-semibold tabular-nums text-zinc-950 dark:text-white">${{ number_format($cartTotal, 2) }}</span>
                </div>
                <p class="text-xs leading-5 text-zinc-500 dark:text-zinc-400">El total no incluye envío. El equipo confirmará disponibilidad y tiempos al revisar tu solicitud.</p>
                <div class="grid gap-2 pt-1">
                    <button type="button" wire:click="saveDraft" wire:loading.attr="disabled" wire:target="saveDraft" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 text-sm font-semibold text-zinc-800 transition hover:border-indigo-400 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-indigo-400 dark:hover:text-indigo-300">
                        <flux:icon.bookmark class="size-4" aria-hidden="true" />
                        <span>Guardar como borrador</span>
                    </button>
                    <button type="button" wire:click="sendQuote" wire:loading.attr="disabled" wire:target="sendQuote" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60 dark:focus:ring-offset-zinc-950">
                        <flux:icon.paper-airplane class="size-4" aria-hidden="true" />
                        <span>Guardar y enviar cotización</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>
</div>
