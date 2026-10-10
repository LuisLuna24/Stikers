<div>
    <flux:table :paginate="$this->orders">
        <flux:table.columns>
            <flux:table.column>Customer</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$sortDirection"
                wire:click="sort('date')">Date</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection"
                wire:click="sort('status')">Status</flux:table.column>
            <flux:table.column sortable :sorted="$sortBy === 'amount'" :direction="$sortDirection"
                wire:click="sort('amount')">Amount</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($this->sizes as $size)
                <flux:table.row :key="$size->id">
                    <flux:table.cell class="flex items-center gap-3">{{ $size->id }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $size->width }}</flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">{{ $size->height }}</flux:table.cell>
                    <flux:table.cell class="py-0">
                        <flux:badge size="sm" :color="$size->is_active ? 'emerald' : 'red'">
                            {{ $size->is_active ? 'Activo' : 'Inactivo' }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell class="py-0">
                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"></flux:button>
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>