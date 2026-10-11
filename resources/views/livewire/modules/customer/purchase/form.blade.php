<div x-data="{
        drawerOpen: false,
        toast: { show: false, message: '', timeoutId: null },
        showToast(message) {
            if (this.toast.timeoutId) clearTimeout(this.toast.timeoutId);
            this.toast.message = message;
            this.toast.show = true;
            this.toast.timeoutId = setTimeout(() => { this.toast.show = false; }, 4500);
        }
    }"
    x-on:notify-toast.window="showToast($event.detail.message)"
    x-on:keydown.escape.window="drawerOpen = false"
    class="min-h-dvh text-zinc-100">

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
        <!-- Header con título y acceso al carrito -->
        <header class="flex flex-col gap-4 border-b border-zinc-800 pb-6 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-400">
                    Cotizador de Stickers
                </p>
                <h1 class="mt-1.5 text-2xl font-semibold tracking-tight text-white sm:text-3xl">
                    Arma tu pedido
                </h1>
                <p class="mt-1.5 text-sm leading-6 text-zinc-300">
                    Configura tamaño, acabado y cantidad con cotización inmediata en tiempo real.
                </p>
            </div>

            <button type="button"
                x-on:click="drawerOpen = true"
                class="relative inline-flex min-h-[44px] items-center justify-center gap-2.5 rounded-xl border border-zinc-700 bg-zinc-900 px-4 py-2.5 text-sm font-semibold text-zinc-100 transition hover:border-indigo-400 hover:text-indigo-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950"
                aria-label="Abrir carrito con {{ $cartItemCount }} productos">
                <flux:icon.shopping-bag class="size-5 text-indigo-400" aria-hidden="true" />
                <span>Carrito</span>
                <span class="inline-flex min-w-6 items-center justify-center rounded-full bg-indigo-600 px-2 py-0.5 text-xs font-bold tabular-nums text-white">
                    {{ $cartItemCount }}
                </span>
            </button>
        </header>

        <!-- Alerta de error de carrito -->
        @error('cart')
            <div role="alert"
                class="flex items-start gap-3 rounded-xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <flux:icon.exclamation-triangle class="mt-0.5 size-5 shrink-0 text-rose-400" aria-hidden="true" />
                <span>{{ $message }}</span>
            </div>
        @enderror

        <!-- Catálogo de productos -->
        <section aria-labelledby="catalog-heading">
            <div class="mb-5 flex items-end justify-between gap-3">
                <div>
                    <h2 id="catalog-heading" class="text-lg font-semibold tracking-tight text-white">
                        Catálogo disponible
                    </h2>
                    <p class="mt-0.5 text-sm text-zinc-300">
                        Selecciona medidas, acabado y cantidad para cada diseño.
                    </p>
                </div>
                <p class="text-sm font-medium tabular-nums text-zinc-400">
                    {{ $designs->count() }} {{ $designs->count() === 1 ? 'diseño' : 'diseños' }}
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($designs as $design)
                    @php
                        $data = $catalogData[$design->id];
                        $firstVariant = $data['variants'][0];
                        $previewUrl = $this->previewUrl($design->preview_image_url);
                    @endphp

                    <article wire:key="design-{{ $design->id }}"
                        x-data="{
                            catalog: @js($data),
                            baseImage: @js($previewUrl),
                            selectedSizeId: @js($firstVariant['size_id']),
                            selectedFinishId: @js($firstVariant['finish_id']),
                            quantity: @js($firstVariant['minimum_quantity']),
                            added: false,
                            quantityErrorMessage: '',

                            formatPrice(value) {
                                return new Intl.NumberFormat('es-MX', {
                                    style: 'currency',
                                    currency: 'MXN',
                                    maximumFractionDigits: 2
                                }).format(value || 0);
                            },
                            selectedVariant() {
                                return this.catalog.variants.find(item => Number(item.size_id) === Number(this.selectedSizeId) && Number(item.finish_id) === Number(this.selectedFinishId))
                                    || this.catalog.variants.find(item => Number(item.size_id) === Number(this.selectedSizeId))
                                    || this.catalog.variants[0];
                            },
                            selectedSize() {
                                return this.catalog.sizes.find(item => Number(item.id) === Number(this.selectedSizeId)) || this.catalog.sizes[0];
                            },
                            selectedFinish() {
                                return this.catalog.finishes.find(item => Number(item.id) === Number(this.selectedFinishId)) || this.catalog.finishes[0];
                            },
                            selectedImage() {
                                return this.selectedVariant()?.finish_image_url || this.baseImage;
                            },
                            imageScaleStyle() {
                                const s = this.selectedSize();
                                if (!s) return '';
                                const maxDim = Math.max(Number(s.width_cm || 5), Number(s.height_cm || 5));
                                const allMaxDims = this.catalog.sizes.map(sz => Math.max(Number(sz.width_cm || 5), Number(sz.height_cm || 5)));
                                const minD = Math.min(...allMaxDims);
                                const maxD = Math.max(...allMaxDims);
                                let scale = 1;
                                if (maxD > minD) {
                                    scale = 0.76 + (0.24 * ((maxDim - minD) / (maxD - minD)));
                                }
                                return `transform: scale(${scale.toFixed(2)});`;
                            },
                            minQuantity() {
                                return Number(this.selectedVariant()?.minimum_quantity || 5);
                            },
                            quantityIncrement() {
                                return Number(this.selectedVariant()?.quantity_increment || 5);
                            },
                            sizeAvailable(sizeId) {
                                return this.catalog.variants.some(item => Number(item.size_id) === Number(sizeId) && Number(item.finish_id) === Number(this.selectedFinishId));
                            },
                            finishAvailable(finishId) {
                                return this.catalog.variants.some(item => Number(item.finish_id) === Number(finishId) && Number(item.size_id) === Number(this.selectedSizeId));
                            },
                            selectSize(sizeId) {
                                if (this.sizeAvailable(sizeId)) {
                                    this.selectedSizeId = sizeId;
                                } else {
                                    const firstForSize = this.catalog.variants.find(item => Number(item.size_id) === Number(sizeId));
                                    if (firstForSize) {
                                        this.selectedSizeId = sizeId;
                                        this.selectedFinishId = firstForSize.finish_id;
                                    }
                                }
                                this.syncQuantity();
                            },
                            selectFinish(finishId) {
                                if (this.finishAvailable(finishId)) {
                                    this.selectedFinishId = finishId;
                                } else {
                                    const firstForFinish = this.catalog.variants.find(item => Number(item.finish_id) === Number(finishId));
                                    if (firstForFinish) {
                                        this.selectedFinishId = finishId;
                                        this.selectedSizeId = firstForFinish.size_id;
                                    }
                                }
                                this.syncQuantity();
                            },
                            quantityIsValid() {
                                const min = this.minQuantity();
                                const inc = this.quantityIncrement();
                                const value = Number(this.quantity);
                                return Number.isInteger(value) && value >= min && (value - min) % inc === 0;
                            },
                            syncQuantity() {
                                const min = this.minQuantity();
                                if (Number(this.quantity) < min) {
                                    this.quantity = min;
                                }
                            },
                            validateQuantityOnBlur() {
                                const min = this.minQuantity();
                                const inc = this.quantityIncrement();
                                let val = Number(this.quantity);
                                if (isNaN(val) || val < min) {
                                    this.quantity = min;
                                    this.quantityErrorMessage = `El mínimo es ${min} piezas.`;
                                    setTimeout(() => {
                                        if (this.quantityErrorMessage === `El mínimo es ${min} piezas.`) {
                                            this.quantityErrorMessage = '';
                                        }
                                    }, 3500);
                                } else if ((val - min) % inc !== 0) {
                                    this.quantityErrorMessage = `Usa múltiplos de ${inc}.`;
                                } else {
                                    this.quantityErrorMessage = '';
                                }
                            },
                            setQuantity(qty) {
                                this.quantity = Number(qty);
                                this.quantityErrorMessage = '';
                            },
                            decrementQuantity() {
                                const min = this.minQuantity();
                                const inc = this.quantityIncrement();
                                const current = Number(this.quantity || min);
                                this.quantity = Math.max(min, current - inc);
                                this.quantityErrorMessage = '';
                            },
                            incrementQuantity() {
                                const min = this.minQuantity();
                                const inc = this.quantityIncrement();
                                const current = Number(this.quantity || min);
                                this.quantity = Math.max(min, current) + inc;
                                this.quantityErrorMessage = '';
                            },
                            sizePrice(sizeId) {
                                const variant = this.catalog.variants.find(item => Number(item.size_id) === Number(sizeId) && Number(item.finish_id) === Number(this.selectedFinishId));
                                return variant ? this.formatPrice(variant.price) : 'No disponible';
                            },
                            finishPrice(finishId) {
                                const variant = this.catalog.variants.find(item => Number(item.finish_id) === Number(finishId) && Number(item.size_id) === Number(this.selectedSizeId));
                                return variant ? this.formatPrice(variant.price) : 'No disponible';
                            },
                            finishPriceDiff(finishId) {
                                const variant = this.catalog.variants.find(item => Number(item.finish_id) === Number(finishId) && Number(item.size_id) === Number(this.selectedSizeId));
                                if (!variant) return null;
                                const variantsForCurrentSize = this.catalog.variants.filter(item => Number(item.size_id) === Number(this.selectedSizeId));
                                const basePrice = Math.min(...variantsForCurrentSize.map(item => item.price));
                                const diff = variant.price - basePrice;
                                return diff > 0 ? `+${this.formatPrice(diff)}` : null;
                            },
                            minPrice() {
                                return Math.min(...this.catalog.variants.map(item => item.price));
                            },
                            getFinishDescription(finish) {
                                const defaults = {
                                    'Mate': 'Sin brillo, ideal para interior y uso diario.',
                                    'Vinil resistente': 'Resiste agua y sol, ideal para exterior.',
                                    'Brillante': 'Refleja la luz y realza colores vivos.',
                                    'Holográfico': 'Efecto arcoíris metálico llamativo.',
                                    'Transparente': 'Fondo invisible, ideal para vidrios o superficies claras.'
                                };
                                if (finish.description && !finish.description.startsWith('Acabado ')) {
                                    return finish.description;
                                }
                                return defaults[finish.name] || finish.description || 'Acabado premium de alta durabilidad.';
                            },
                            submitAddToCart() {
                                if (!this.quantityIsValid()) {
                                    this.validateQuantityOnBlur();
                                    return;
                                }
                                if (this.added) return;

                                this.added = true;
                                const variant = this.selectedVariant();
                                const qty = Number(this.quantity);
                                const sizeDim = this.selectedSize()?.dimensions || this.selectedSize()?.name || '';
                                const finishName = this.selectedFinish()?.name || '';
                                const designName = this.catalog.design_name || @js($design->name);

                                $wire.addToCart(Number(variant.id), qty);

                                $dispatch('notify-toast', {
                                    message: `Agregaste ${qty} × ${designName} (${sizeDim}, ${finishName})`
                                });

                                setTimeout(() => {
                                    this.added = false;
                                }, 1500);
                            }
                        }"
                        class="relative flex flex-col rounded-2xl border border-zinc-800 bg-zinc-950 text-zinc-100 shadow-sm transition duration-200 hover:border-zinc-700">

                        <!-- Preview de imagen y proporciones dinámicas -->
                        <div class="relative aspect-square overflow-hidden rounded-t-2xl bg-zinc-900/60 p-6 flex items-center justify-center">
                            <img x-show="selectedImage()"
                                x-bind:src="selectedImage()"
                                x-bind:alt="`${selectedSize()?.name || ''} ${selectedFinish()?.name || ''} de ${@js($design->name)}`"
                                loading="lazy"
                                width="800"
                                height="800"
                                x-bind:style="imageScaleStyle()"
                                class="max-h-full max-w-full object-contain transition-transform duration-300 ease-out motion-reduce:transition-none" />

                            <div x-show="!selectedImage()"
                                class="flex size-full items-center justify-center text-zinc-600">
                                <flux:icon.photo class="size-14" aria-hidden="true" />
                                <span class="sr-only">Sin vista previa disponible</span>
                            </div>

                            <!-- Disponibilidad sutil superior -->
                            <div class="absolute inset-x-3.5 top-3.5 flex items-center justify-between">
                                <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-500/20 bg-zinc-950/75 px-2.5 py-1 text-[11px] font-medium text-emerald-300 backdrop-blur-sm">
                                    <span class="size-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span>
                                    Disponible
                                </span>
                            </div>

                            <!-- Badges informativos inferiores (planos, no botones) -->
                            <div class="absolute inset-x-3.5 bottom-3.5 flex items-center justify-between gap-2 text-[11px] font-medium text-zinc-300">
                                <span class="rounded-md border border-zinc-800 bg-zinc-950/80 px-2.5 py-1 backdrop-blur-sm"
                                    x-text="selectedSize()?.dimensions || ''"></span>
                                <span class="rounded-md border border-zinc-800 bg-zinc-950/80 px-2.5 py-1 backdrop-blur-sm"
                                    x-text="selectedFinish()?.name || ''"></span>
                            </div>
                        </div>

                        <!-- Contenido y configuración de cotización -->
                        <div class="flex flex-1 flex-col gap-5 p-4 sm:p-5">
                            <!-- Título, SKU discreto y precio base -->
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="font-mono text-[11px] uppercase tracking-wider text-zinc-400">{{ $design->sku }}</span>
                                    <h3 class="mt-0.5 text-lg font-semibold tracking-tight text-white sm:text-xl">
                                        {{ $design->name }}
                                    </h3>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Desde</span>
                                    <span class="text-lg font-bold tabular-nums text-white sm:text-xl"
                                        x-text="formatPrice(minPrice())"></span>
                                </div>
                            </div>

                            <!-- Selector de Tamaño (RadioGroup accesible) -->
                            <section>
                                <div class="mb-2">
                                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-300">
                                        Tamaño
                                    </label>
                                </div>
                                <div role="radiogroup" aria-label="Selecciona un tamaño"
                                    class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                    <template x-for="size in catalog.sizes" :key="size.id">
                                        <button type="button"
                                            role="radio"
                                            x-on:click="selectSize(size.id)"
                                            x-bind:disabled="!sizeAvailable(size.id)"
                                            x-bind:aria-checked="Number(selectedSizeId) === Number(size.id)"
                                            x-bind:class="Number(selectedSizeId) === Number(size.id)
                                                ? 'border-2 border-indigo-500 bg-indigo-500/10 text-white shadow-sm ring-1 ring-indigo-500/30'
                                                : 'border border-zinc-800 bg-zinc-900/90 text-zinc-300 hover:border-zinc-700 hover:bg-zinc-800/80 hover:text-white'"
                                            class="relative flex min-h-[58px] flex-col justify-between rounded-xl p-2.5 text-left transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950 disabled:cursor-not-allowed disabled:opacity-30">

                                            <!-- Checkmark en esquina para opción activa -->
                                            <span x-show="Number(selectedSizeId) === Number(size.id)"
                                                class="absolute top-2 right-2 flex size-4 items-center justify-center rounded-full bg-indigo-600 text-white"
                                                aria-hidden="true">
                                                <svg class="size-2.5" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="2.5 6 4.8 8.5 9.5 3.5"/>
                                                </svg>
                                            </span>

                                            <div class="pr-4">
                                                <span class="block text-xs font-semibold text-white" x-text="size.name"></span>
                                                <span class="mt-0.5 block text-[11px] font-medium text-zinc-400" x-text="size.dimensions"></span>
                                            </div>
                                            <span class="mt-2 block text-xs font-semibold tabular-nums text-indigo-300"
                                                x-text="sizePrice(size.id)"></span>
                                        </button>
                                    </template>
                                </div>
                            </section>

                            <!-- Selector de Acabado (RadioGroup accesible con precio dinámico y descripción) -->
                            <section>
                                <div class="mb-2">
                                    <label class="text-xs font-semibold uppercase tracking-wider text-zinc-300">
                                        Acabado
                                    </label>
                                </div>
                                <div role="radiogroup" aria-label="Selecciona un acabado"
                                    class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
                                    <template x-for="finish in catalog.finishes" :key="finish.id">
                                        <button type="button"
                                            role="radio"
                                            x-on:click="selectFinish(finish.id)"
                                            x-bind:disabled="!finishAvailable(finish.id)"
                                            x-bind:aria-checked="Number(selectedFinishId) === Number(finish.id)"
                                            x-bind:class="Number(selectedFinishId) === Number(finish.id)
                                                ? 'border-2 border-indigo-500 bg-indigo-500/10 text-white shadow-sm ring-1 ring-indigo-500/30'
                                                : 'border border-zinc-800 bg-zinc-900/90 text-zinc-300 hover:border-zinc-700 hover:bg-zinc-800/80 hover:text-white'"
                                            class="relative flex min-h-[64px] flex-col justify-between rounded-xl p-3 text-left transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950 disabled:cursor-not-allowed disabled:opacity-30">

                                            <!-- Checkmark en esquina para opción activa -->
                                            <span x-show="Number(selectedFinishId) === Number(finish.id)"
                                                class="absolute top-2.5 right-2.5 flex size-4 items-center justify-center rounded-full bg-indigo-600 text-white"
                                                aria-hidden="true">
                                                <svg class="size-2.5" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="2.5 6 4.8 8.5 9.5 3.5"/>
                                                </svg>
                                            </span>

                                            <div>
                                                <div class="flex items-center gap-2 pr-6">
                                                    <span class="size-2.5 shrink-0 rounded-full" x-bind:class="finish.dot_class" aria-hidden="true"></span>
                                                    <span class="text-xs font-semibold text-white" x-text="finish.name"></span>
                                                </div>
                                            </div>

                                            <div class="mt-2.5 flex items-baseline justify-between border-t border-zinc-800/80 pt-2">
                                                <span class="text-[10px] font-medium uppercase text-zinc-400">Unitario</span>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-xs font-semibold tabular-nums text-indigo-300"
                                                        x-text="finishPrice(finish.id)"></span>
                                                    <template x-if="finishPriceDiff(finish.id)">
                                                        <span class="text-[10px] font-medium tabular-nums text-zinc-400"
                                                            x-text="`(${finishPriceDiff(finish.id)})`"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                </div>
                            </section>

                            <!-- Selector de Cantidad (Stepper centrado + Chips rápidos) -->
                            <section>
                                <div class="mb-2 flex items-center justify-between">
                                    <label for="quantity-{{ $design->id }}"
                                        class="text-xs font-semibold uppercase tracking-wider text-zinc-300">
                                        Cantidad
                                    </label>
                                    <span class="text-xs font-medium text-zinc-300"
                                        x-text="`Mínimo ${minQuantity()} piezas`"></span>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <!-- Control Stepper: botones >= 44x44px e input numérico centrado -->
                                    <div class="w-full flex items-center justify-between rounded-xl border border-zinc-800 bg-zinc-900/90 p-1">
                                        <button type="button"
                                            x-on:click="decrementQuantity()"
                                            x-bind:disabled="Number(quantity) <= minQuantity()"
                                            class="inline-flex size-11 min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-lg font-bold text-zinc-300 transition hover:bg-zinc-800 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:bg-transparent disabled:hover:text-zinc-300"
                                            aria-label="Disminuir cantidad">
                                            −
                                        </button>
                                        <input id="quantity-{{ $design->id }}"
                                            type="number"
                                            inputmode="numeric"
                                            x-model.number="quantity"
                                            x-on:blur="validateQuantityOnBlur()"
                                            x-bind:min="minQuantity()"
                                            x-bind:step="quantityIncrement()"
                                            class="h-11 w-16 border-0 bg-transparent p-0 text-center text-base font-bold tabular-nums text-white focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                            aria-describedby="quantity-help-{{ $design->id }}" />
                                        <button type="button"
                                            x-on:click="incrementQuantity()"
                                            class="inline-flex size-11 min-h-[44px] min-w-[44px] items-center justify-center rounded-lg text-lg font-bold text-zinc-300 transition hover:bg-zinc-800 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                                            aria-label="Aumentar cantidad">
                                            +
                                        </button>
                                    </div>
                                </div>

                                <!-- Mensaje de error/validación visible -->
                                <div x-cloak x-show="quantityErrorMessage"
                                    class="mt-2 flex items-center gap-1.5 text-xs font-medium text-rose-400"
                                    role="alert">
                                    <svg class="size-4 shrink-0" viewBox="0 0 16 16" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm-.75-4.25a.75.75 0 1 1 1.5 0 .75.75 0 0 1-1.5 0zM8 4a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 0 1.5 0v-4.5A.75.75 0 0 0 8 4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span x-text="quantityErrorMessage"></span>
                                </div>
                            </section>

                            <!-- Resumen simplificado (1 línea compacta + Total protagonista) -->
                            <section class="rounded-xl border border-zinc-800 bg-zinc-900/90 p-3.5">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold tabular-nums text-zinc-100 sm:text-base"
                                            x-text="`${quantity} × ${formatPrice(selectedVariant()?.price)}`"></p>
                                        <p class="mt-0.5 truncate text-xs text-zinc-400"
                                            x-text="`${selectedSize()?.dimensions || ''} · ${selectedFinish()?.name || ''}`"></p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="block text-[10px] font-semibold uppercase tracking-wider text-zinc-400">Total</span>
                                        <span class="text-2xl font-bold tabular-nums text-white sm:text-3xl"
                                            x-text="formatPrice((selectedVariant()?.price || 0) * (Number(quantity) || 0))"></span>
                                    </div>
                                </div>
                            </section>

                            <!-- Botón de acción: sticky en móvil con '$525.00 · Agregar al carrito' y feedback '✓ Agregado' -->
                            <div class="sticky bottom-0 z-10 -mx-4 -mb-4 mt-auto border-t border-zinc-800/90 bg-zinc-950/95 p-4 backdrop-blur-md sm:static sm:mx-0 sm:mb-0 sm:border-0 sm:bg-transparent sm:p-0">
                                <button type="button"
                                    x-on:click="submitAddToCart()"
                                    x-bind:disabled="!quantityIsValid() || added"
                                    wire:loading.attr="disabled"
                                    wire:target="addToCart"
                                    class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950 disabled:cursor-not-allowed disabled:opacity-50">
                                    <!-- Estado '✓ Agregado' durante ~1.5 s -->
                                    <template x-if="added">
                                        <span class="inline-flex items-center gap-1.5 text-sm font-bold text-white">
                                            <svg class="size-4 shrink-0" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <polyline points="3 8 6.5 11.5 13 4"/>
                                            </svg>
                                            Agregado
                                        </span>
                                    </template>
                                    <!-- Estado normal: en móvil muestra '$Total · Agregar al carrito' -->
                                    <template x-if="!added">
                                        <span class="inline-flex items-center gap-2">
                                            <flux:icon.plus class="size-4" aria-hidden="true" />
                                            <span class="sm:hidden"
                                                x-text="`${formatPrice((selectedVariant()?.price || 0) * (Number(quantity) || 0))} · Agregar al carrito`"></span>
                                            <span class="hidden sm:inline">Agregar al carrito</span>
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-zinc-800 bg-zinc-900/50 px-6 py-14 text-center">
                        <flux:icon.rectangle-stack class="mx-auto size-10 text-zinc-500" aria-hidden="true" />
                        <h3 class="mt-4 font-semibold text-white">El catálogo está en preparación</h3>
                        <p class="mx-auto mt-1 max-w-md text-sm text-zinc-400">
                            En cuanto haya diseños disponibles podrás agregarlos a tu cotización desde aquí.
                        </p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    <!-- Toast de feedback al agregar al carrito con botón 'Ver carrito' -->
    <div x-cloak x-show="toast.show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:translate-x-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed bottom-5 right-5 z-50 flex max-w-md items-center gap-3 rounded-2xl border border-zinc-700/80 bg-zinc-900/95 p-4 shadow-2xl shadow-black/60 backdrop-blur-md"
        role="status" aria-live="polite">
        <div class="flex size-7 shrink-0 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-400">
            <svg class="size-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="3 8 6.5 11.5 13 4" />
            </svg>
        </div>
        <div class="min-w-0 flex-1 text-xs text-zinc-200">
            <p class="font-medium text-white" x-text="toast.message"></p>
        </div>
        <button type="button"
            x-on:click="drawerOpen = true; toast.show = false"
            class="shrink-0 rounded-lg px-2.5 py-1 text-xs font-semibold text-indigo-400 transition hover:bg-indigo-500/10 hover:text-indigo-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
            Ver carrito
        </button>
        <button type="button"
            x-on:click="toast.show = false"
            class="shrink-0 rounded-lg p-1 text-zinc-400 transition hover:bg-zinc-800 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400"
            aria-label="Cerrar notificación">
            <flux:icon.x-mark class="size-4" aria-hidden="true" />
        </button>
    </div>

    <!-- Drawer lateral del carrito -->
    <div x-cloak x-show="drawerOpen" class="fixed inset-0 z-50" role="presentation">
        <button type="button"
            x-on:click="drawerOpen = false"
            class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm"
            aria-label="Cerrar carrito"></button>

        <aside x-show="drawerOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 flex w-full max-w-md flex-col border-l border-zinc-800 bg-zinc-900 shadow-2xl"
            role="dialog" aria-modal="true" aria-labelledby="cart-heading">

            <div class="flex items-start justify-between gap-4 border-b border-zinc-800 px-5 py-5">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-400">
                        Resumen
                    </p>
                    <h2 id="cart-heading" class="mt-1 text-xl font-semibold tracking-tight text-white">
                        Tu carrito
                    </h2>
                    <p class="mt-1 text-sm text-zinc-400">
                        {{ $cartItemCount }} {{ $cartItemCount === 1 ? 'línea' : 'líneas' }} seleccionadas
                    </p>
                </div>
                <button type="button"
                    x-on:click="drawerOpen = false"
                    class="inline-flex size-11 items-center justify-center rounded-xl text-zinc-400 transition hover:bg-zinc-800 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                    aria-label="Cerrar carrito">
                    <flux:icon.x-mark class="size-5" aria-hidden="true" />
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-5 py-5">
                @forelse ($cartItems as $item)
                    @php $variant = $item['variant']; @endphp
                    <article wire:key="cart-{{ $variant->id }}"
                        class="flex gap-3 border-b border-zinc-800 py-4 first:pt-0 last:border-b-0">
                        <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-400">
                            <flux:icon.photo class="size-5" aria-hidden="true" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="truncate text-sm font-semibold text-white">
                                        {{ $variant->design->name }}
                                    </h3>
                                    <p class="mt-0.5 text-xs text-zinc-400">
                                        {{ $variant->stickerSize->name }} ({{ $variant->stickerSize->width_cm }} × {{ $variant->stickerSize->height_cm }} cm) · {{ $variant->stickerFinish->name }}
                                    </p>
                                </div>
                                <button type="button"
                                    wire:click="removeFromCart({{ $variant->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="removeFromCart({{ $variant->id }})"
                                    class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg text-zinc-400 transition hover:bg-rose-500/10 hover:text-rose-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-500"
                                    aria-label="Eliminar {{ $variant->design->name }} del carrito">
                                    <flux:icon.trash class="size-4" aria-hidden="true" />
                                </button>
                            </div>
                            <div class="mt-3 flex items-end justify-between gap-3">
                                <div>
                                    <label for="cart-quantity-{{ $variant->id }}"
                                        class="text-xs font-medium text-zinc-400">
                                        Cantidad
                                    </label>
                                    <input id="cart-quantity-{{ $variant->id }}"
                                        type="number"
                                        min="{{ $variant->minimum_quantity }}"
                                        step="{{ $variant->quantity_increment }}"
                                        value="{{ $item['quantity'] }}"
                                        wire:change="updateCartQuantity({{ $variant->id }}, $event.target.value)"
                                        class="mt-1 min-h-[44px] w-24 rounded-lg border border-zinc-700 bg-zinc-950 px-2.5 text-sm tabular-nums text-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                        aria-describedby="cart-help-{{ $variant->id }}" />
                                    <p id="cart-help-{{ $variant->id }}" class="mt-1 text-[11px] text-zinc-400">
                                        Mín. {{ $variant->minimum_quantity }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-base font-bold tabular-nums text-white">
                                        ${{ number_format($item['subtotal'], 2) }}
                                    </p>
                                    <span class="mt-0.5 block text-xs text-zinc-400">
                                        ${{ number_format((float) $variant->unit_price, 2) }} / pza
                                    </span>
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="flex min-h-64 flex-col items-center justify-center text-center">
                        <div class="flex size-14 items-center justify-center rounded-full border border-dashed border-zinc-700 text-zinc-500">
                            <flux:icon.shopping-bag class="size-6" aria-hidden="true" />
                        </div>
                        <h3 class="mt-4 font-semibold text-white">Tu carrito está vacío</h3>
                        <p class="mt-1 max-w-xs text-sm leading-6 text-zinc-400">
                            Selecciona un diseño y configura su tamaño, acabado y cantidad para empezar.
                        </p>
                        <button type="button"
                            x-on:click="drawerOpen = false"
                            class="mt-5 inline-flex min-h-[44px] items-center rounded-xl px-4 text-sm font-semibold text-indigo-400 transition hover:bg-indigo-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                            Explorar catálogo
                        </button>
                    </div>
                @endforelse
            </div>

            <div class="space-y-3.5 border-t border-zinc-800 bg-zinc-950 p-5">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm font-medium text-zinc-300">Total</span>
                    <span class="text-2xl font-bold tabular-nums text-white">
                        ${{ number_format($cartTotal, 2) }}
                    </span>
                </div>
                <p class="text-xs leading-5 text-zinc-400">
                    El total no incluye envío. El equipo confirmará disponibilidad y tiempos al revisar tu solicitud.
                </p>
                <div class="grid gap-2.5 pt-1">
                    <button type="button"
                        wire:click="saveDraft"
                        wire:loading.attr="disabled"
                        wire:target="saveDraft"
                        class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl border border-zinc-700 bg-zinc-900 px-4 text-sm font-semibold text-zinc-200 transition hover:border-indigo-400 hover:text-indigo-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
                        <flux:icon.bookmark class="size-4" aria-hidden="true" />
                        <span>Guardar como borrador</span>
                    </button>
                    <button type="button"
                        wire:click="sendQuote"
                        wire:loading.attr="disabled"
                        wire:target="sendQuote"
                        class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white transition hover:bg-indigo-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-zinc-950 disabled:cursor-not-allowed disabled:opacity-60">
                        <flux:icon.paper-airplane class="size-4" aria-hidden="true" />
                        <span>Guardar y enviar cotización</span>
                    </button>
                </div>
            </div>
        </aside>
    </div>
</div>