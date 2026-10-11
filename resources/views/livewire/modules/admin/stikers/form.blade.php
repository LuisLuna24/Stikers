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
        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-200">
                <p class="font-medium">No se pudo guardar el diseño. Revisa estos campos:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-5">
                <h2 class="font-medium text-zinc-900 dark:text-white">Información del diseño</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">El slug se genera automáticamente a partir del nombre.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <x-w-input wire:model.live="sku" label="SKU" placeholder="Ej. STK-001" />
                <x-w-input wire:model.live="name" label="Nombre" placeholder="Ej. Logo de temporada" />
                <div class="md:col-span-2">
                    <x-w-input wire:model="vector_file" type="file" accept=".svg,.pdf,.ai,.eps" label="Archivo vectorial (opcional)" />
                    @if ($vector_file_url)
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Ya existe un archivo vectorial cargado. Solo selecciona otro si deseas reemplazarlo.</p>
                    @endif
                    @error('vector_file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <x-w-input wire:model="preview_image" type="file" accept="image/jpeg,image/png,image/webp" label="Imagen de vista previa" />
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
                <p class="text-sm text-zinc-500 dark:text-zinc-400 md:col-span-2">Los borradores quedan inactivos. La publicación valida que todos los datos y combinaciones estén completos.</p>
            </div>
        </section>

        <section class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex size-7 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">2</span>
                        <h2 class="font-medium text-zinc-900 dark:text-white">Arma las variantes</h2>
                    </div>
                    <p class="mt-2 max-w-2xl text-sm text-zinc-500 dark:text-zinc-400">Elige los tamaños y acabados disponibles. El sistema creará automáticamente una combinación por cada cruce.</p>
                </div>
                @if ($variants !== [])
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300">
                        <flux:icon.check-circle class="size-4" /> {{ count($variants) }} {{ count($variants) === 1 ? 'combinación' : 'combinaciones' }}
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-medium text-zinc-900 dark:text-white">1. ¿Qué tamaños venderás?</h3>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Selecciona uno o varios.</p>
                        </div>
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ count($selectedSizeIds) }} seleccionados</span>
                    </div>
                    @if ($sizes->isEmpty())
                        <div class="rounded-lg border border-dashed border-amber-300 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-200">No hay tamaños activos en el catálogo.</div>
                    @else
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach ($sizes as $size)
                                <label wire:key="size-choice-{{ $size->id }}" class="flex min-h-16 cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 p-3 transition hover:border-indigo-300 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:border-zinc-700 dark:hover:border-indigo-700 dark:has-[:checked]:border-indigo-500 dark:has-[:checked]:bg-indigo-950/30">
                                    <input type="checkbox" wire:model.live="selectedSizeIds" value="{{ $size->id }}" class="size-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800">
                                    <span class="min-w-0">
                                        <span class="block text-sm font-medium text-zinc-900 dark:text-white">{{ $size->name }}</span>
                                        <span class="block text-xs text-zinc-500 dark:text-zinc-400">{{ $size->width_cm }} × {{ $size->height_cm }} cm</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <div class="mb-3 flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-medium text-zinc-900 dark:text-white">2. ¿Qué acabados ofrecerás?</h3>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Puedes reutilizar uno o crear uno nuevo.</p>
                        </div>
                        <x-w-button type="button" wire:click="addFinish" icon="plus" size="sm">Nuevo acabado</x-w-button>
                    </div>

                    @error('newFinishes') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror
                    <div class="space-y-3">
                        @foreach ($finishes as $finish)
                            <label wire:key="finish-choice-{{ $finish->id }}" class="flex min-h-16 cursor-pointer items-center gap-3 rounded-lg border border-zinc-200 p-3 transition hover:border-indigo-300 has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50 dark:border-zinc-700 dark:hover:border-indigo-700 dark:has-[:checked]:border-indigo-500 dark:has-[:checked]:bg-indigo-950/30">
                                <input type="checkbox" wire:model.live="selectedFinishKeys" value="finish-{{ $finish->id }}" class="size-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800">
                                @if ($finish->getFirstMediaUrl('image'))
                                    <img src="{{ $finish->getFirstMediaUrl('image') }}" alt="Referencia {{ $finish->name }}" class="size-10 rounded-md border border-zinc-200 object-cover dark:border-zinc-700" />
                                @else
                                    <span class="flex size-10 items-center justify-center rounded-md border border-dashed border-zinc-300 text-xs text-zinc-400 dark:border-zinc-600">—</span>
                                @endif
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $finish->name }}</span>
                            </label>
                        @endforeach
                        @foreach ($newFinishes as $finishIndex => $newFinish)
                            <div wire:key="new-finish-{{ $finishIndex }}" class="rounded-lg border border-indigo-200 bg-indigo-50/50 p-3 dark:border-indigo-900 dark:bg-indigo-950/20">
                                <div class="flex items-start gap-3">
                                    <input type="checkbox" wire:model.live="selectedFinishKeys" value="new-{{ $finishIndex }}" class="mt-1 size-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800">
                                    <div class="min-w-0 flex-1 space-y-3">
                                        <x-w-input wire:model.live="newFinishes.{{ $finishIndex }}.name" label="Nombre del acabado nuevo" placeholder="Ej. Mate" />
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            <x-w-input wire:model="newFinishes.{{ $finishIndex }}.image" type="file" accept="image/jpeg,image/png,image/webp" label="Imagen de referencia" />
                                            <x-w-textarea wire:model="newFinishes.{{ $finishIndex }}.description" label="Descripción (opcional)" rows="2" />
                                        </div>
                                    </div>
                                    <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeFinish({{ $finishIndex }})" aria-label="Quitar acabado nuevo" />
                                </div>
                                @if ($newFinish['image'])
                                    <img src="{{ $newFinish['image']->temporaryUrl() }}" alt="Referencia nueva" class="mt-3 size-14 rounded-md object-cover" />
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if ($finishes->isEmpty() && $newFinishes === [])
                        <p class="rounded-lg border border-dashed border-zinc-300 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">Agrega al menos un acabado para poder generar variantes.</p>
                    @endif
                </div>
            </div>

            <div class="my-6 flex flex-col items-center justify-between gap-3 rounded-lg bg-zinc-50 p-4 sm:flex-row dark:bg-zinc-800/60">
                <p class="text-sm text-zinc-600 dark:text-zinc-300">¿Ya elegiste las opciones? Genera las combinaciones y después asigna sus precios.</p>
                <x-w-button type="button" wire:click="generateVariants" icon="sparkles" wire:loading.attr="disabled" wire:target="generateVariants">Generar combinaciones</x-w-button>
            </div>

            @error('variants') <p class="mb-4 text-sm text-red-600">{{ $message }}</p> @enderror
            <div class="space-y-4">
                @forelse ($variants as $index => $variant)
                    <div wire:key="variant-{{ $index }}-{{ $variant['sticker_size_id'] ?? 'none' }}-{{ $variant['finish_key'] ?? 'none' }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">Combinación {{ $index + 1 }}</p>
                                @error("variants.{$index}") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removeVariant({{ $index }})" aria-label="Eliminar combinación {{ $index + 1 }}" />
                        </div>
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                            <div>
                                <p class="mb-1 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Tamaño</p>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ optional($sizes->firstWhere('id', $variant['sticker_size_id'] ?? null))->name ?? 'Sin seleccionar' }}</p>
                            </div>
                            <div>
                                <p class="mb-1 text-xs font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Acabado</p>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $finishes->firstWhere('id', str_replace('finish-', '', $variant['finish_key'] ?? ''))?->name ?? (str_starts_with($variant['finish_key'] ?? '', 'new-') ? ($newFinishes[(int) str_replace('new-', '', $variant['finish_key'])]['name'] ?? 'Acabado nuevo') : 'Sin seleccionar') }}</p>
                            </div>
                            <x-w-input wire:model="variants.{{ $index }}.unit_price" type="number" min="0.01" step="0.01" label="Precio unitario" placeholder="0.00" />
                            <x-w-input wire:model="variants.{{ $index }}.minimum_quantity" type="number" min="5" max="5" label="Mínimo" />
                            <div class="flex items-center gap-3 pt-7">
                                <input id="variants-{{ $index }}-active" type="checkbox" wire:model="variants.{{ $index }}.is_active" class="size-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800">
                                <label for="variants-{{ $index }}-active" class="text-sm text-zinc-700 dark:text-zinc-300">Activa</label>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-lg border border-dashed border-zinc-300 p-6 text-center dark:border-zinc-700">
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Todavía no hay combinaciones</p>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Selecciona al menos un tamaño y un acabado, y presiona “Generar combinaciones”.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('admin.stikers.index') }}" variant="ghost" wire:navigate>Cancelar</flux:button>
            <flux:button type="button" variant="ghost" wire:click="saveDraft" wire:loading.attr="disabled" wire:target="saveDraft">
                <span wire:loading.remove wire:target="saveDraft">Guardar borrador</span>
                <span wire:loading wire:target="saveDraft">Guardando...</span>
            </flux:button>
            <flux:button type="button" variant="primary" wire:click="publish" wire:loading.attr="disabled" wire:target="publish">
                <span wire:loading.remove wire:target="publish">Publicar diseño</span>
                <span wire:loading wire:target="publish">Publicando...</span>
            </flux:button>
        </div>
    </form>
</div>
