<footer class="bg-slate-900 py-12 text-xs text-slate-400">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 border-b border-slate-800 pb-10 md:grid-cols-4">
            <div class="space-y-3 md:col-span-2">
                <div class="flex items-center gap-2 font-display text-lg font-bold text-white">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-orange-600 text-xs text-white">
                        TB</div><span>TWO<span class="text-orange-500">BROTHERS</span></span>
                </div>
                <p class="max-w-sm leading-relaxed">Stickers de vinil de alto rendimiento y mayoreo directo para
                    marcas, creadores y negocios en todo México.</p>
            </div>
            <div>
                <h4 class="mb-3 text-sm font-bold text-white">Navegación</h4>
                <div class="space-y-2"><a href="{{ route('home') }}"
                        class="block transition-colors hover:text-orange-400">Inicio</a><a
                        href="{{ route('catalogs') }}"
                        class="block transition-colors hover:text-orange-400">Catálogo</a><a
                        href="{{ route('contact') }}" class="block text-orange-400">Contacto</a></div>
            </div>
            <div>
                <h4 class="mb-3 text-sm font-bold text-white">Atención</h4>
                <p class="leading-relaxed">Lunes a viernes<br>9:00 a 18:00 hrs.</p><a href="https://wa.me/5215500000000"
                    class="mt-3 inline-block font-semibold text-orange-400 hover:text-orange-300">WhatsApp directo
                    →</a>
            </div>
        </div>
        <div class="flex flex-col items-center justify-between gap-3 pt-7 text-[11px] sm:flex-row">
            <p>&copy; {{ date('Y') }} Two Brothers Stickers. Todos los derechos reservados.</p>
            <p>Hecho para que tu marca se quede pegada.</p>
        </div>
    </div>
</footer>
