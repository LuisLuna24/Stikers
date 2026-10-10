<div>
    <div class="flex flex-col sm:items-center sm:justify-between gap-4 mb-6">
        <!-- Título -->
        <div class="w-full">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Tamaños de los stickers</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Administra las dimensiones disponibles en el sistema.</p>
        </div>

        <!-- Acciones (Buscador y Botón) -->
        <div class="w-full flex items-center justify-between gap-3">
            <div class="w-2xl">
                <x-w-input wire:model.live.debounce.300ms="search" placeholder="Buscar..." icon="magnifying-glass" />
            </div>
            <x-w-button wire:click="create" icon="plus">
                Nuevo tamaño
            </x-w-button>
        </div>
    </div>
    <flux:table :paginate="$sizes" bleed>
        <flux:table.columns sticky>
            <flux:table.column>ID</flux:table.column>
            <flux:table.column>Ancho</flux:table.column>
            <flux:table.column>Alto</flux:table.column>
            <flux:table.column>Estatus</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($sizes as $size)
                <flux:table.row :key="$size->id">
                    <flux:table.cell class="flex items-center gap-3">{{ $size->id }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $size->width_cm }} cm</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $size->height_cm }} cm</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <div class="flex items-center gap-3">
                            <flux:switch
                                :checked="$size->is_active"
                                wire:click="toggleStatus({{ $size->id }})"
                                aria-label="Cambiar estatus de {{ $size->name }}"
                            />
                            <span class="text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $size->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell class="py-0 text-right">
                        <flux:button variant="ghost" size="sm" icon="pencil" wire:click="edit({{ $size->id }})" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="py-12 text-center">
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


    <x-w-modal-card :title="$sizeId ? 'Editar tamaño' : 'Nuevo tamaño'" name="cardModal">
        <form wire:submit="save">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <x-w-input wire:model="name" label="Nombre" placeholder="Ej. Mediano" class="col-span-1 sm:col-span-2" />

                <x-w-input wire:model="width_cm" type="number" step="0.01" label="Ancho (cm)" placeholder="Ej. 100" />
                <x-w-input wire:model="height_cm" type="number" step="0.01" label="Alto (cm)" placeholder="Ej. 200" />
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
