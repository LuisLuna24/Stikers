<div class="mx-auto max-w-5xl space-y-6">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ $customerId ? 'Editar cliente' : 'Nuevo cliente' }}</flux:heading>
            <flux:subheading class="mt-1">Registra los datos básicos y, si los tienes, su domicilio y datos de facturación.</flux:subheading>
        </div>
        <flux:button href="{{ route('admin.customers.index') }}" variant="ghost" icon="arrow-left" wire:navigate aria-label="Volver a clientes" />
    </div>

    <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 sm:p-8">
        <div class="border-b border-zinc-200 pb-5 dark:border-zinc-700">
            <flux:heading size="lg">{{ $customerId ? 'Editar información del cliente' : 'Dar de alta un cliente' }}</flux:heading>
            <flux:text class="mt-1">Los campos marcados con <span class="text-red-600">*</span> son obligatorios.</flux:text>
        </div>

        <form wire:submit="save" class="space-y-8 pt-6">
                @if ($errors->any())
                    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/30 dark:text-red-200">
                        <p class="font-medium">Revisa la información antes de continuar.</p>
                        <p class="mt-1">Hay campos pendientes o con formato incorrecto.</p>
                    </div>
                @endif

                <section class="space-y-4">
                    <div>
                        <h3 class="font-medium text-zinc-900 dark:text-white">Información de contacto</h3>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Usaremos estos datos para identificar al cliente y contactarlo sobre sus pedidos.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-w-select wire:model.live="customer_type" label="Tipo de cliente *" :options="[['name' => 'Persona', 'id' => 'individual'], ['name' => 'Negocio', 'id' => 'business']]" option-label="name" option-value="id" />
                        <x-w-input wire:model="contact_name" label="Nombre o razón social *" placeholder="Ej. Ana López o Estudio Norte" autocomplete="name" />
                        <x-w-input wire:model="phone" type="tel" label="Teléfono *" placeholder="10 dígitos" autocomplete="tel" />
                        <x-w-input wire:model="email" type="email" label="Correo electrónico *" placeholder="cliente@ejemplo.com" autocomplete="email" />
                    </div>
                    <div class="rounded-lg border border-indigo-100 bg-indigo-50/70 p-3 text-sm text-indigo-900 dark:border-indigo-900/60 dark:bg-indigo-950/30 dark:text-indigo-200">
                        Se generará el acceso del cliente automáticamente con este correo. Podrá establecer su contraseña desde “¿Olvidaste tu contraseña?”.
                    </div>
                </section>

                <section class="space-y-4 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                    <div class="flex items-start gap-3">
                        <input id="has-address" type="checkbox" wire:model.live="hasAddress" class="mt-1 size-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800" />
                        <label for="has-address" class="cursor-pointer">
                            <span class="font-medium text-zinc-900 dark:text-white">Agregar domicilio principal</span>
                            <span class="mt-1 block text-sm text-zinc-500 dark:text-zinc-400">Puedes capturarlo ahora o agregarlo cuando registres su primer pedido.</span>
                        </label>
                    </div>

                    @if ($hasAddress)
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-w-input wire:model="address_label" label="Nombre del domicilio" placeholder="Principal" />
                            <x-w-input wire:model="recipient_name" label="Recibe *" placeholder="Nombre de quien recibe" />
                            <x-w-input wire:model="address_phone" type="tel" label="Teléfono del domicilio" placeholder="10 dígitos" />
                            <x-w-input wire:model="street" label="Calle *" placeholder="Ej. Av. Reforma" />
                            <x-w-input wire:model="exterior_number" label="Número exterior" placeholder="Ej. 120" />
                            <x-w-input wire:model="interior_number" label="Número interior" placeholder="Ej. 4B" />
                            <x-w-input wire:model="neighborhood" label="Colonia *" placeholder="Ej. Centro" />
                            <x-w-input wire:model="postal_code" label="Código postal *" placeholder="Ej. 06000" inputmode="numeric" />
                            <x-w-input wire:model="city" label="Ciudad *" placeholder="Ej. Ciudad de México" />
                            <x-w-input wire:model="municipality" label="Municipio o alcaldía" placeholder="Ej. Cuauhtémoc" />
                            <x-w-input wire:model="state" label="Estado *" placeholder="Ej. CDMX" />
                            <x-w-input wire:model="country" label="País *" placeholder="México" />
                            <div class="sm:col-span-2">
                                <x-w-textarea wire:model="references" label="Referencias (opcional)" rows="2" placeholder="Entre calles, color de fachada, indicaciones de entrega..." />
                            </div>
                        </div>
                    @endif
                </section>

                <section class="space-y-4 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                    <div class="flex items-start gap-3">
                        <input id="wants-invoice" type="checkbox" wire:model.live="wantsInvoice" class="mt-1 size-5 rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500 dark:border-zinc-600 dark:bg-zinc-800" />
                        <label for="wants-invoice" class="cursor-pointer">
                            <span class="font-medium text-zinc-900 dark:text-white">Guardar datos para facturación</span>
                            <span class="mt-1 block text-sm text-zinc-500 dark:text-zinc-400">Actívalo solo si el cliente necesita factura; podrás completarlos más adelante.</span>
                        </label>
                    </div>

                    @if ($wantsInvoice)
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <x-w-input wire:model="legal_name" label="Nombre fiscal *" placeholder="Razón social o nombre completo" />
                            </div>
                            <x-w-input wire:model="rfc" label="RFC *" placeholder="Ej. XAXX010101000" maxlength="13" />
                            <x-w-input wire:model="fiscal_regime_code" label="Régimen fiscal *" placeholder="Ej. 601" />
                            <x-w-input wire:model="fiscal_postal_code" label="Código postal fiscal *" placeholder="Ej. 06000" />
                            <x-w-input wire:model="cfdi_use_code" label="Uso de CFDI *" placeholder="Ej. G03" />
                            <div class="sm:col-span-2">
                                <x-w-input wire:model="fiscal_email" type="email" label="Correo para factura" placeholder="facturacion@ejemplo.com" />
                            </div>
                        </div>
                    @endif
                </section>

                <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 pt-5 sm:flex-row sm:justify-end dark:border-zinc-700">
                    <x-w-button type="button" outline negative label="Cancelar" href="{{ route('admin.customers.index') }}" wire:navigate />
                    <x-w-button type="submit" primary label="{{ $customerId ? 'Guardar cambios' : 'Registrar cliente' }}" spinner="save" />
                </div>
        </form>
    </div>
</div>
