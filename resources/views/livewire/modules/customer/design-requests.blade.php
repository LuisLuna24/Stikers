<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Personaliza tu pedido</p>
            <flux:heading size="xl" class="mt-1">Solicitudes de diseño</flux:heading>
            <flux:text class="mt-1">Cuéntanos qué necesitas y el equipo te ayudará a convertir tu idea en stickers.</flux:text>
        </div>
        <flux:button wire:click="openForm" variant="primary" icon="plus">
            Nueva solicitud
        </flux:button>
    </div>

    <flux:modal name="customer-design-request" :closable="false" class="w-full max-w-2xl" scroll="body">
        <div class="space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <flux:heading size="lg">Nueva solicitud</flux:heading>
                    <flux:text class="mt-1">Comparte una referencia visual y los detalles principales de tu idea.</flux:text>
                </div>
                <flux:button type="button" wire:click="closeForm" variant="ghost" icon="x-mark" aria-label="Cerrar formulario" />
            </div>

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 dark:border-rose-400/30 dark:bg-rose-400/10 dark:text-rose-200" role="alert">
                    <p class="font-semibold">Revisa la información antes de enviar.</p>
                    <p class="mt-1">Encontramos uno o más campos que necesitan atención.</p>
                </div>
            @endif

            <form wire:submit="save" class="space-y-5">
                <div class="grid gap-5 sm:grid-cols-2">
                    <flux:input wire:model="title" label="Título de la solicitud" placeholder="Ej. Stickers para mi cafetería" required />
                    <flux:input wire:model="design_reference_url" type="url" label="URL de referencia" placeholder="https://..." description="Comparte un enlace a la imagen o idea de referencia." required />
                    <flux:select wire:model="desired_size_id" label="Tamaño preferido" description="Opcional">
                        <option value="">Cualquier tamaño</option>
                        @foreach ($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }} · {{ $size->width_cm }} × {{ $size->height_cm }} cm</option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="desired_finish_id" label="Acabado preferido" description="Opcional">
                        <option value="">Cualquier acabado</option>
                        @foreach ($finishes as $finish)
                            <option value="{{ $finish->id }}">{{ $finish->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="requested_quantity" type="number" min="5" step="5" label="Cantidad aproximada" description="Mínimo 5 stickers y múltiplos de 5." placeholder="Ej. 50" />
                    <div class="flex items-end">
                        <p class="rounded-xl bg-indigo-50 px-4 py-3 text-sm leading-5 text-indigo-900 dark:bg-indigo-400/10 dark:text-indigo-200">El equipo revisará tu solicitud y te contactará con disponibilidad y cotización.</p>
                    </div>
                    <div class="sm:col-span-2">
                        <flux:textarea wire:model="description" label="Cuéntanos más" placeholder="Describe colores, texto, medidas especiales o el uso que tendrán tus stickers." rows="4" />
                    </div>
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 pt-5 sm:flex-row sm:justify-end dark:border-zinc-700">
                    <flux:button type="button" wire:click="closeForm" variant="ghost">Cancelar</flux:button>
                    <flux:button type="submit" variant="primary" icon="paper-airplane" wire:loading.attr="disabled" wire:target="save">
                        Enviar solicitud
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <flux:card>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="lg">Historial de solicitudes</flux:heading>
                <flux:text class="mt-1">{{ number_format($requests->total()) }} solicitudes registradas.</flux:text>
            </div>
            <flux:select wire:model.live="status" aria-label="Filtrar solicitudes por estado" class="w-full sm:w-52">
                <option value="">Todos los estados</option>
                <option value="submitted">Nuevas</option>
                <option value="reviewing">En revisión</option>
                <option value="quoted">Cotizadas</option>
                <option value="accepted">Aceptadas</option>
                <option value="declined">Rechazadas</option>
                <option value="converted_to_order">Convertidas a pedido</option>
                <option value="cancelled">Canceladas</option>
            </flux:select>
        </div>

        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            @forelse ($requests as $request)
                <article wire:key="customer-design-request-{{ $request->id }}" class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <h2 class="truncate font-semibold text-zinc-950 dark:text-white">{{ $request->title }}</h2>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Enviada el {{ $request->created_at?->format('d/m/Y · H:i') }}</p>
                        </div>
                        <x-dashboard.status-badge :label="$this->statusLabel($request->status)" :classes="$this->statusClasses($request->status)" :dot="$this->statusDotClasses($request->status)" />
                    </div>

                    <p class="mt-4 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $request->description ?: 'Sin descripción adicional.' }}</p>

                    <dl class="mt-4 grid grid-cols-2 gap-3 border-t border-zinc-200 pt-4 text-sm dark:border-zinc-700">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-500">Referencia</dt>
                            <dd class="mt-1"><a href="{{ $request->design_reference_url }}" target="_blank" rel="noopener noreferrer" class="break-all font-medium text-indigo-600 hover:underline dark:text-indigo-400">Abrir enlace</a></dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-500">Cantidad</dt>
                            <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $request->requested_quantity ? number_format($request->requested_quantity).' piezas' : 'Por definir' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-500">Tamaño</dt>
                            <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $request->desiredSize?->name ?: 'Por definir' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-zinc-500">Acabado</dt>
                            <dd class="mt-1 font-medium text-zinc-900 dark:text-white">{{ $request->desiredFinish?->name ?: 'Por definir' }}</dd>
                        </div>
                    </dl>

                    @if ($request->response_note || $request->quoted_unit_price)
                        <div class="mt-4 rounded-xl bg-zinc-50 p-3 text-sm dark:bg-zinc-800/70">
                            @if ($request->response_note)
                                <p class="font-medium text-zinc-900 dark:text-white">Respuesta del equipo</p>
                                <p class="mt-1 text-zinc-600 dark:text-zinc-300">{{ $request->response_note }}</p>
                            @endif
                            @if ($request->quoted_unit_price)
                                <p class="mt-2 font-semibold text-indigo-700 dark:text-indigo-300">Cotización: ${{ number_format((float) $request->quoted_unit_price, 2) }} por pieza</p>
                            @endif
                        </div>
                    @endif
                </article>
            @empty
                <div class="lg:col-span-2">
                    <x-dashboard.empty-state title="Todavía no tienes solicitudes" description="Crea una solicitud y cuéntanos cómo imaginas tu próximo diseño." icon="document-text">
                        <flux:button wire:click="openForm" variant="ghost" icon="plus">Crear solicitud</flux:button>
                    </x-dashboard.empty-state>
                </div>
            @endforelse
        </div>

        @if ($requests->hasPages())
            <div class="mt-5 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                {{ $requests->links() }}
            </div>
        @endif
    </flux:card>
</div>
