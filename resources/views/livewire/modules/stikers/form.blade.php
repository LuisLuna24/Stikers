<div>
    <div class="mb-8 flex flex-col gap-2">
        <div class="flex items-center gap-3">
            <flux:button href="{{ route('admin.stikers.index') }}" variant="ghost" icon="arrow-left" wire:navigate />
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                {{ $designId ? 'Editar stiker' : 'Crear stiker' }}
            </h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400">Define el diseño y agrega únicamente las variantes que este stiker necesita.</p>
    </div>

    <form wire:submit="save" class="space-y-8">
        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-5">
                <h2 class="font-medium text-zinc-900 dark:text-white">Información del diseño</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">El slug se genera automáticamente a partir del nombre.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-w-input wire:model.live="sku" label="SKU" placeholder="Ej. STK-001" />
                <x-w-input wire:model.live="name" label="Nombre" placeholder="Ej. Logo de temporada" />
                <div class="md:col-span-2">
                    <x-w-input wire:model="vector_file_url" type="url" label="URL del archivo vectorial (opcional)" placeholder="https://..." />
                </div>
                <div class="md:col-span-2">
                    <x-w-input wire:model="preview_image" type="file" accept="image/jpeg,image/png,image/webp" label="Imagen de vista previa (opcional)" />
                    @if ($preview_image)
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ $preview_image->temporaryUrl() }}" alt="Nueva vista previa" class="size-20 rounded-lg border border-zinc-200 object-cover dark:border-zinc-700" />
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Nueva imagen seleccionada</span>
                        </div>
                    @elseif ($current_preview_image_url)
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ $current_preview_image_url }}" alt="Vista previa actual" class="size-20 rounded-lg border border-zinc-200 object-cover dark:border-zinc-700" />
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Vista previa actual</span>
                        </div>
                    @endif
                </div>
                <div class="md:col-span-2">
                    <x-w-textarea wire:model="description" label="Descripción" rows="4" placeholder="Describe el diseño o sus instrucciones de producción..." />
                </div>
                <div class="flex items-center gap-3">
                    <input id="is_active" type="checkbox" wire:model="is_active" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800">
                    <label for="is_active" class="text-sm text-zinc-700 dark:text-zinc-300">Diseño activo</label>
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="font-medium text-zinc-900 dark:text-white">Variantes y precios</h2>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Agrega las combinaciones de tamaño y acabado disponibles para este stiker.</p>
                </div>
                <x-w-button type="button" wire:click="addVariant" icon="plus">Agregar variante</x-w-button>
            </div>

            @error('variants')
                <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($sizes->isEmpty() || $finishes->isEmpty())
                <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 p-5 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-200">
                    Debes tener al menos un tamaño y un acabado activos antes de agregar variantes.
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($variants as $index => $variant)
                        <div wire:key="variant-{{ $index }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                            <div class="mb-4 flex items-center justify-between">
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">Variante {{ $index + 1 }}</span>
                                <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeVariant({{ $index }})" aria-label="Eliminar variante {{ $index + 1 }}" />
                            </div>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-6">
                                <div class="xl:col-span-1">
                                    <label for="variants-{{ $index }}-size" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Tamaño</label>
                                    <select id="variants-{{ $index }}-size" wire:model="variants.{{ $index }}.sticker_size_id" class="w-full rounded-lg border-zinc-300 bg-white text-sm dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                                        <option value="">Selecciona</option>
                                        @foreach ($sizes as $size)
                                            <option value="{{ $size->id }}">{{ $size->name }} ({{ $size->width_cm }} × {{ $size->height_cm }} cm)</option>
                                        @endforeach
                                    </select>
                                    @error("variants.{$index}.sticker_size_id") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div class="xl:col-span-1">
                                    <label for="variants-{{ $index }}-finish" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Acabado</label>
                                    <select id="variants-{{ $index }}-finish" wire:model="variants.{{ $index }}.sticker_finish_id" class="w-full rounded-lg border-zinc-300 bg-white text-sm dark:border-zinc-600 dark:bg-zinc-800 dark:text-white">
                                        <option value="">Selecciona</option>
                                        @foreach ($finishes as $finish)
                                            <option value="{{ $finish->id }}">{{ $finish->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("variants.{$index}.sticker_finish_id") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <x-w-input wire:model="variants.{{ $index }}.unit_price" type="number" min="0.01" step="0.01" label="Precio unitario" placeholder="0.00" />
                                <x-w-input wire:model="variants.{{ $index }}.minimum_quantity" type="number" min="1" label="Cantidad mínima" />
                                <x-w-input wire:model="variants.{{ $index }}.quantity_increment" type="number" min="1" label="Incremento" />
                                <div class="flex items-center gap-3 pt-7">
                                    <input id="variants-{{ $index }}-active" type="checkbox" wire:model="variants.{{ $index }}.is_active" class="size-4 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800">
                                    <label for="variants-{{ $index }}-active" class="text-sm text-zinc-700 dark:text-zinc-300">Activa</label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('admin.stikers.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Guardar stiker</span>
                <span wire:loading wire:target="save">Guardando...</span>
            </flux:button>
        </div>
    </form>
</div>
