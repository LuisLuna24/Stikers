<div>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Catálogo de stickers</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Administra diseños, combinaciones, precios y publicación.</p>
        </div>
        <div class="flex gap-3">
            <x-w-input wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o SKU..." icon="magnifying-glass" />
            <x-w-button href="{{ route('admin.stikers.create') }}" icon="plus" wire:navigate>Agregar diseño</x-w-button>
        </div>
    </div>

    <flux:table :paginate="$designs" bleed>
        <flux:table.columns sticky>
            <flux:table.column>Diseño</flux:table.column>
            <flux:table.column>SKU</flux:table.column>
            <flux:table.column>Combinaciones activas</flux:table.column>
            <flux:table.column>Estatus</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($designs as $design)
                <flux:table.row :key="$design->id">
                    <flux:table.cell>
                        <div class="flex items-center gap-3">
                            @if ($design->preview_image_url)
                                <img src="{{ \Illuminate\Support\Str::startsWith($design->preview_image_url, ['http://', 'https://']) ? $design->preview_image_url : \Illuminate\Support\Facades\Storage::disk('public')->url($design->preview_image_url) }}" alt="{{ $design->name }}" class="size-10 rounded object-cover" />
                            @endif
                            <span class="font-medium">{{ $design->name }}</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $design->sku }}</flux:table.cell>
                    <flux:table.cell>{{ $design->active_variants_count }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <div class="flex items-center gap-3">
                            <flux:switch :checked="$design->is_active" wire:click="toggleStatus({{ $design->id }})" aria-label="Cambiar estatus de {{ $design->name }}" />
                            <span class="text-sm">{{ $design->is_active ? 'Activo' : 'Borrador / inactivo' }}</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="py-0 text-right">
                        <flux:button href="{{ route('admin.stikers.edit', $design->id) }}" variant="ghost" size="sm" icon="pencil" wire:navigate aria-label="Editar {{ $design->name }}" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row><flux:table.cell colspan="5" class="py-12 text-center">No se encontraron diseños.</flux:table.cell></flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
