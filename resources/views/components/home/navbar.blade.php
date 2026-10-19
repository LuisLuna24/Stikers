<!-- Top Announcement Strip -->
<div
    class="bg-gradient-to-r from-orange-950 via-orange-900 to-amber-950 text-orange-100 text-xs py-2 px-4 border-b border-orange-800/40">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center gap-2 tracking-wide font-medium">
            <span
                class="inline-flex items-center justify-center px-1.5 py-0.5 rounded bg-orange-500/20 text-orange-300 font-bold text-[10px] uppercase border border-orange-400/30">Mayoreo
                Directo</span>
            <span>Stickers por mayoreo a corte de vinil</span>
        </div>
        <div class="flex items-center gap-4 text-[11px] text-orange-200/80">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                        clip-rule="evenodd"></path>
                </svg>
                Catalogo y pedidos digitales
            </span>
            <span class="hidden md:inline-block text-orange-600">•</span>
            <span class="hidden md:flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z">
                    </path>
                    <path
                        d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z">
                    </path>
                </svg>
                Envíos a todo México
            </span>
        </div>
    </div>
</div>

<!-- Main Navigation Header -->
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-orange-100/80 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand / Logo -->
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 group focus:outline-hidden focus-visible:ring-2 focus-visible:ring-orange-500 rounded-lg p-1">
                <div
                    class="w-11 h-11 rounded-xl bg-gradient-to-br from-orange-500 via-orange-600 to-amber-700 flex items-center justify-center text-white shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform duration-200 border border-orange-400/30">
                    <svg class="w-6 h-6 transform -rotate-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <path d="M10 12l4 4m0-4l-4 4"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span
                        class="font-display font-bold text-xl tracking-tight text-slate-900 group-hover:text-orange-600 transition-colors">
                        TWO<span class="text-orange-600 font-extrabold">BROTHERS</span>
                    </span>
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-orange-700/80 -mt-0.5">
                        Stickers & Dessigns
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden lg:flex items-center gap-8 font-medium text-sm text-slate-700">
                <a href="{{ route('home') }}"
                    class="hover:text-orange-600 transition-colors flex items-center gap-1.5 {{ request()->routeIs('home') ? 'text-orange-600 font-semibold border-b-2 border-orange-600' : '' }}">
                    Inicio
                </a>
                <a href="{{ route('catalogs') }}" 
                    class="hover:text-orange-600 transition-colors flex items-center gap-1.5 {{ request()->routeIs('catalogs') ? 'text-orange-600 font-semibold border-b-2 border-orange-600' : '' }}">
                    <span>Catálogo</span>
                </a>
                <a href="{{ route('contact') }}" class="hover:text-orange-600 transition-colors">
                    Contacto
                </a>
            </nav>

            <!-- Actions / CTAs -->
            <div class="hidden sm:flex items-center gap-3">
                @unless (request()->routeIs('contact'))
                    <button @click="quoteModal = true"
                        class="px-4 py-2.5 text-xs font-semibold text-orange-800 bg-orange-50 hover:bg-orange-100 rounded-xl transition-all border border-orange-200/90 active:scale-95">
                        Cotizar a Medida
                    </button>
                @endunless
                <a href="{{ route('catalogs') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 rounded-xl shadow-md shadow-orange-600/25 transition-all active:scale-95">
                    <span>Ver Catálogo</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="p-2.5 text-slate-600 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-colors"
                            title="Mi Panel">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="p-2.5 text-slate-600 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-colors text-xs font-semibold"
                            title="Iniciar sesión">
                            Entrar
                        </a>
                    @endauth
                @endif
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenu = !mobileMenu"
                class="lg:hidden p-2.5 text-slate-700 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-colors focus:outline-hidden"
                aria-label="Abrir menú de navegación">
                <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7">
                    </path>
                </svg>
                <svg x-show="mobileMenu" class="w-6 h-6" x-cloak fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Nav Dropdown -->
    <div x-show="mobileMenu" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden border-b border-orange-100 bg-white/98 px-5 pt-3 pb-6 space-y-3 shadow-xl">
        <a href="{{ route('home') }}" @click="mobileMenu = false"
            class="block py-2 font-semibold text-orange-600 border-l-2 border-orange-600 pl-3">
            Inicio
        </a>
        <a href="{{ route('catalogs') }}" @click="mobileMenu = false"
            class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
            Catálogo de Stickers (40+ Modelos)
        </a>
        <a href="#calculadora" @click="mobileMenu = false"
            class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
            Calculadora de Mayoreo
        </a>
        <a href="#acabados" @click="mobileMenu = false"
            class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
            Materiales & Acabados
        </a>
        <a href="#proceso" @click="mobileMenu = false"
            class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
            Cómo Funciona el Pedido
        </a>
        <a href="#faq" @click="mobileMenu = false"
            class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
            Preguntas Frecuentes
        </a>
        <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
            <a href="{{ route('catalogs') }}"
                class="w-full text-center py-3 text-sm font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 rounded-xl">
                Explorar Todo el Catálogo
            </a>
            @unless (request()->routeIs('contact'))
                <button @click="quoteModal = true; mobileMenu = false"
                    class="w-full text-center py-2.5 text-sm font-semibold text-orange-800 bg-orange-50 rounded-xl border border-orange-200">
                    Cotizar Proyecto Especial
                </button>
            @endunless
        </div>
    </div>
</header>
