<div>
    <div class="flex flex-col gap-4 mb-6 sm:items-center sm:justify-between">
        <div class="w-full">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Acabados de los stickers</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Administra los acabados disponibles en el sistema.</p>
        </div>

        <div class="flex items-center justify-between w-full gap-3">
            <div class="w-2xl">
                <x-w-input wire:model.live.debounce.300ms="search" placeholder="Buscar..." icon="magnifying-glass" />
            </div>
            <x-w-button wire:click="create" icon="plus">
                Nuevo acabado
            </x-w-button>
        </div>
    </div>

    <flux:table :paginate="$finishes" bleed>
        <flux:table.columns sticky>
            <flux:table.column>ID</flux:table.column>
            <flux:table.column>Nombre</flux:table.column>
            <flux:table.column>Descripción</flux:table.column>
            <flux:table.column>Imagen</flux:table.column>
            <flux:table.column>Estatus</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($finishes as $finish)
                <flux:table.row :key="$finish->id">
                    <flux:table.cell>{{ $finish->id }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $finish->name }}</flux:table.cell>
                    <flux:table.cell class="max-w-xs truncate">
                        {{ $finish->description ?: '—' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($finish->getFirstMediaUrl('image'))
                            <a href="{{ $finish->getFirstMediaUrl('image') }}" target="_blank" rel="noopener noreferrer"
                                class="text-sm text-blue-600 hover:underline dark:text-blue-400">
                                Ver imagen
                            </a>
                        @else
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">—</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="py-0">
                        <div class="flex items-center gap-3">
                            <flux:switch
                                :checked="$finish->is_active"
                                wire:click="toggleStatus({{ $finish->id }})"
                                aria-label="Cambiar estatus de {{ $finish->name }}"
                            />
                            <span class="text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $finish->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="py-0 text-right">
                        <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $finish->id }})" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <flux:icon name="inbox" class="size-8 text-zinc-400 dark:text-zinc-500" />
                            <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">
                                No se encontraron registros
                            </span>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <x-w-modal-card :title="$finishId ? 'Editar acabado' : 'Nuevo acabado'" name="finishModal">
        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4">
                <x-w-input wire:model="name" label="Nombre" placeholder="Ej. Mate" />

                <x-w-textarea wire:model="description" label="Descripción"
                    placeholder="Describe las características del acabado..." rows="4" />

                <x-w-input wire:model="image" type="file" accept="image/jpeg,image/png,image/webp"
                    label="Imagen" />

                @if ($image)
                    <div class="flex items-center gap-3">
                        <img src="{{ $image->temporaryUrl() }}" alt="Vista previa" class="size-16 rounded object-cover" />
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Nueva imagen seleccionada</span>
                    </div>
                @elseif ($finishId && $currentImageUrl)
                    <div class="flex items-center gap-3">
                        <img src="{{ $currentImageUrl }}" alt="Imagen actual" class="size-16 rounded object-cover" />
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Imagen actual</span>
                    </div>
                @endif
            </div>

            <x-slot name="footer" class="flex justify-end gap-x-4">
                <div class="flex gap-x-4">
                    <x-w-button type="button" outline negative label="Cancelar" x-on:click="close" />
                    <x-w-button type="button" primary label="Guardar" wire:click="save" spinner="save" />
                </div>
            </x-slot>
        </form>
    </x-w-modal-card>
</div>
