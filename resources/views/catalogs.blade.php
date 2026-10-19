<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catálogo de Stickers al Mayoreo | Luna Stickers</title>
    <meta name="description" content="Explora nuestro catálogo de stickers de vinil listos para producción al mayoreo. Acabados tornasol, mate, brillante y metálico en múltiples tamaños.">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & Vite Assets -->
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                50: '#fff7ed',
                                100: '#ffedd5',
                                200: '#fed7aa',
                                300: '#fdba74',
                                400: '#fb923c',
                                500: '#f97316',
                                600: '#ea580c',
                                700: '#c2410c',
                                800: '#9a3412',
                                900: '#7c2d12',
                                950: '#431407',
                            }
                        },
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                            display: ['"Space Grotesk"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            background-color: #fafaf9;
        }
        .font-display {
            font-family: 'Space Grotesk', system-ui, sans-serif;
        }
        .bg-grid-pattern {
            background-size: 28px 28px;
            background-image: 
                linear-gradient(to right, rgba(234, 88, 12, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(234, 88, 12, 0.04) 1px, transparent 1px);
        }
    </style>
</head>
<body class="min-h-screen bg-[#fafaf9] text-slate-900 selection:bg-orange-500 selection:text-white"
      x-data="catalogApp()">

    @php
        // Resilient fallback if variable is missing
        if (!isset($designs) || $designs->isEmpty()) {
            $designs = \App\Models\Design::query()
                ->active()
                ->with(['variants' => fn ($q) => $q->active()->with(['stickerSize', 'stickerFinish'])])
                ->orderBy('name')
                ->get();
        }

        if (!isset($sizes)) {
            $sizes = \App\Models\StickerSize::query()->where('is_active', true)->orderBy('name')->get();
        }

        if (!isset($finishes)) {
            $finishes = \App\Models\StickerFinish::query()->where('is_active', true)->orderBy('name')->get();
        }

        // Format designs data for ultra-fast Alpine client filtering
        $designsJson = $designs->map(function ($d) {
            $firstVariant = $d->variants->first();
            $sizeName = $firstVariant?->stickerSize?->name ?? 'Estándar';
            $sizeDimensions = ($firstVariant?->stickerSize) 
                ? ($firstVariant->stickerSize->width_cm . ' x ' . $firstVariant->stickerSize->height_cm . ' cm') 
                : '7 x 7 cm';
            $finishName = $firstVariant?->stickerFinish?->name ?? 'Básico';
            $basePrice = $firstVariant?->unit_price ? (float)$firstVariant->unit_price : 8.00;

            return [
                'id' => $d->id,
                'name' => $d->name,
                'sku' => $d->sku,
                'slug' => $d->slug,
                'description' => $d->description ?: 'Sticker en vinil polimérico 4mil de alta adherencia con protección contra agua y rayos UV.',
                'image' => $d->preview_image_url ? asset('storage/' . $d->preview_image_url) : null,
                'size' => $sizeName,
                'dimensions' => $sizeDimensions,
                'finish' => $finishName,
                'retailPrice' => $basePrice,
                'wholesalePrice' => number_format($basePrice * 0.35, 2),
            ];
        })->values()->toJson();
    @endphp

    <!-- Top Announcement Strip -->
    <div class="bg-gradient-to-r from-orange-950 via-orange-900 to-amber-950 text-orange-100 text-xs py-2 px-4 border-b border-orange-800/40">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2 tracking-wide font-medium">
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded bg-orange-500/20 text-orange-300 font-bold text-[10px] uppercase border border-orange-400/30">Mayoreo Directo</span>
                <span>Precios de fábrica en tirajes desde 50 piezas por modelo</span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-orange-200/80">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    Muestra digital previa en &lt; 2 hrs
                </span>
                <span class="hidden md:inline-block text-orange-600">•</span>
                <span>Envío asegurado a todo México</span>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-orange-100/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand / Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-hidden focus-visible:ring-2 focus-visible:ring-orange-500 rounded-lg p-1">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-orange-500 via-orange-600 to-amber-700 flex items-center justify-center text-white shadow-md shadow-orange-500/20 group-hover:scale-105 transition-transform duration-200 border border-orange-400/30">
                        <svg class="w-6 h-6 transform -rotate-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <path d="M10 12l4 4m0-4l-4 4"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display font-bold text-xl tracking-tight text-slate-900 group-hover:text-orange-600 transition-colors">
                            LUNA<span class="text-orange-600 font-extrabold">STICKERS</span>
                        </span>
                        <span class="text-[10px] font-semibold uppercase tracking-widest text-orange-700/80 -mt-0.5">
                            Manufactura & Mayoreo
                        </span>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <nav class="hidden lg:flex items-center gap-8 font-medium text-sm text-slate-700">
                    <a href="{{ route('home') }}" class="hover:text-orange-600 transition-colors">
                        Inicio
                    </a>
                    <a href="{{ route('catalogs') }}" class="text-orange-600 font-semibold relative after:absolute after:bottom-[-6px] after:left-0 after:right-0 after:h-0.5 after:bg-orange-600 after:rounded-full flex items-center gap-1.5">
                        <span>Catálogo</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full border border-orange-200">
                            {{ count($designs) }} Modelos
                        </span>
                    </a>
                    <a href="{{ route('home') }}#calculadora" class="hover:text-orange-600 transition-colors">
                        Calculadora
                    </a>
                    <a href="{{ route('home') }}#acabados" class="hover:text-orange-600 transition-colors">
                        Acabados
                    </a>
                    <a href="{{ route('home') }}#proceso" class="hover:text-orange-600 transition-colors">
                        Cómo Pedir
                    </a>
                    <a href="{{ route('home') }}#faq" class="hover:text-orange-600 transition-colors">
                        Preguntas
                    </a>
                </nav>

                <!-- Actions / CTAs -->
                <div class="hidden sm:flex items-center gap-3">
                    <button @click="customQuoteModal = true"
                            class="px-4 py-2.5 text-xs font-semibold text-orange-800 bg-orange-50 hover:bg-orange-100 rounded-xl transition-all border border-orange-200/90 active:scale-95">
                        Subir Mi Diseño Propio
                    </button>
                    <a href="{{ route('home') }}#calculadora"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 rounded-xl shadow-md shadow-orange-600/25 transition-all active:scale-95">
                        <span>Calculadora de Precios</span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu"
                        class="lg:hidden p-2.5 text-slate-700 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-colors focus:outline-hidden"
                        aria-label="Abrir menú de navegación">
                    <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    <svg x-show="mobileMenu" class="w-6 h-6" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Nav Dropdown -->
        <div x-show="mobileMenu"
             x-cloak
             x-transition
             class="lg:hidden border-b border-orange-100 bg-white/98 px-5 pt-3 pb-6 space-y-3 shadow-xl">
            <a href="{{ route('home') }}" class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
                Inicio
            </a>
            <a href="{{ route('catalogs') }}" class="block py-2 font-semibold text-orange-600 border-l-2 border-orange-600 pl-3">
                Catálogo de Stickers ({{ count($designs) }} Modelos)
            </a>
            <a href="{{ route('home') }}#calculadora" class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
                Calculadora de Mayoreo
            </a>
            <a href="{{ route('home') }}#acabados" class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
                Materiales & Acabados
            </a>
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                <button @click="customQuoteModal = true; mobileMenu = false" class="w-full text-center py-2.5 text-sm font-semibold text-orange-800 bg-orange-50 rounded-xl border border-orange-200">
                    Subir Mi Diseño Propio
                </button>
            </div>
        </div>
    </header>

    <!-- CATALOG HERO & BREADCRUMBS -->
    <section class="bg-gradient-to-b from-orange-50/60 via-white to-[#fafaf9] pt-8 pb-12 border-b border-slate-200/60 relative">
        <div class="absolute inset-0 bg-grid-pattern opacity-60 pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-orange-600">Inicio</a>
                <span>/</span>
                <span class="text-orange-700 font-semibold">Catálogo Mayorista</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="max-w-2xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-800 text-xs font-bold tracking-wide border border-orange-200">
                        <span class="w-2 h-2 rounded-full bg-orange-600"></span>
                        ENTREGA RÁPIDA O PERSONALIZACIÓN
                    </div>
                    <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-950 tracking-tight">
                        Catálogo de Stickers al Mayoreo
                    </h1>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        Colección de diseños listos para imprimir en vinil automotriz 4mil de alta resistencia. 
                        Elige tu modelo, selecciona tu acabado preferido y aprovecha tarifas escalonadas desde 50 piezas.
                    </p>
                </div>

                <!-- Quick specs pill group -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs shrink-0">
                    <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-2xs text-center">
                        <div class="font-display font-black text-lg text-orange-600">{{ count($designs) }}</div>
                        <div class="text-[11px] text-slate-500">Diseños Activos</div>
                    </div>
                    <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-2xs text-center">
                        <div class="font-display font-black text-lg text-slate-900">4</div>
                        <div class="text-[11px] text-slate-500">Acabados Pro</div>
                    </div>
                    <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-2xs text-center">
                        <div class="font-display font-black text-lg text-slate-900">48h</div>
                        <div class="text-[11px] text-slate-500">Tiempo Despacho</div>
                    </div>
                    <div class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-2xs text-center">
                        <div class="font-display font-black text-lg text-emerald-600">-68%</div>
                        <div class="text-[11px] text-slate-500">Ahorro Máximo</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- INTERACTIVE FILTER & SEARCH TOOLBAR (Alpine.js) -->
    <section class="sticky top-20 z-30 bg-white/95 backdrop-blur-md border-b border-orange-200/60 shadow-xs py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-3">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <!-- Search Input with icon -->
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Buscar por nombre o SKU (ej. Cinnamoroll, TWSK-0001)..."
                           class="w-full pl-10 pr-9 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/70 focus:bg-white transition-all">
                    <button x-show="searchQuery"
                            x-cloak
                            @click="searchQuery = ''"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Secondary Filters & View Toggle -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
                    <!-- Sort dropdown -->
                    <select x-model="sortBy"
                            class="py-2 px-3 text-xs rounded-xl border border-slate-300 bg-white text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-orange-500 font-medium">
                        <option value="name_asc">Nombre (A - Z)</option>
                        <option value="name_desc">Nombre (Z - A)</option>
                        <option value="sku_asc">SKU (Menor a Mayor)</option>
                    </select>

                    <!-- Layout Mode Toggle (4 cols vs 2 cols large) -->
                    <div class="hidden sm:flex items-center rounded-xl border border-slate-300 p-0.5 bg-slate-100">
                        <button type="button"
                                @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'bg-white shadow-xs text-orange-600' : 'text-slate-500 hover:text-slate-700'"
                                class="p-1.5 rounded-lg transition-all"
                                title="Vista compacta (4 columnas)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </button>
                        <button type="button"
                                @click="viewMode = 'large'"
                                :class="viewMode === 'large' ? 'bg-white shadow-xs text-orange-600' : 'text-slate-500 hover:text-slate-700'"
                                class="p-1.5 rounded-lg transition-all"
                                title="Vista grande (2 columnas)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                        </button>
                    </div>

                    <!-- Reset Filters Button -->
                    <button x-show="searchQuery || selectedFinish !== 'all' || selectedSize !== 'all'"
                            x-cloak
                            @click="resetFilters()"
                            class="py-2 px-3 text-xs font-semibold text-orange-700 bg-orange-50 hover:bg-orange-100 rounded-xl border border-orange-200 transition-colors whitespace-nowrap">
                        Limpiar filtros
                    </button>
                </div>
            </div>

            <!-- Finish Pill Chips Filter -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <span class="text-slate-600 font-bold text-[11px] uppercase tracking-wider shrink-0 mr-1">Filtrar acabado:</span>
                <button type="button"
                        @click="selectedFinish = 'all'"
                        :class="selectedFinish === 'all' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-xs' : 'bg-white text-slate-700 hover:bg-orange-50 border-slate-200'"
                        class="px-3 py-1.5 rounded-xl border transition-all shrink-0">
                    Todos los acabados
                </button>
                <button type="button"
                        @click="selectedFinish = 'Tornasol'"
                        :class="selectedFinish === 'Tornasol' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-xs' : 'bg-white text-slate-700 hover:bg-orange-50 border-slate-200'"
                        class="px-3 py-1.5 rounded-xl border transition-all shrink-0 flex items-center gap-1">
                    <span>Tornasol Prisma ✨</span>
                </button>
                <button type="button"
                        @click="selectedFinish = 'Basico'"
                        :class="selectedFinish === 'Basico' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-xs' : 'bg-white text-slate-700 hover:bg-orange-50 border-slate-200'"
                        class="px-3 py-1.5 rounded-xl border transition-all shrink-0">
                    Básico / Brillante
                </button>
                <button type="button"
                        @click="selectedFinish = 'Metálico'"
                        :class="selectedFinish === 'Metálico' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-xs' : 'bg-white text-slate-700 hover:bg-orange-50 border-slate-200'"
                        class="px-3 py-1.5 rounded-xl border transition-all shrink-0">
                    Metálico Plata
                </button>
                <button type="button"
                        @click="selectedFinish = 'Sublimado'"
                        :class="selectedFinish === 'Sublimado' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-xs' : 'bg-white text-slate-700 hover:bg-orange-50 border-slate-200'"
                        class="px-3 py-1.5 rounded-xl border transition-all shrink-0">
                    Sublimado / Fantasía
                </button>
            </div>
        </div>
    </section>

    <!-- MAIN CATALOG PRODUCT GRID -->
    <main class="py-12 bg-[#fafaf9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Counter & active filters indicator -->
            <div class="flex items-center justify-between text-xs text-slate-500 mb-6 pb-2 border-b border-slate-200/80">
                <div>
                    Mostrando <strong class="text-slate-900" x-text="filteredDesigns.length"></strong> de <strong>{{ count($designs) }}</strong> stickers disponibles
                </div>
                <div class="text-[11px] text-orange-700 font-semibold bg-orange-50 px-2.5 py-1 rounded-md border border-orange-200">
                    Descuento de mayoreo automático desde 50 pzas
                </div>
            </div>

            <!-- Empty State -->
            <div x-show="filteredDesigns.length === 0"
                 x-cloak
                 class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 max-w-lg mx-auto my-12 space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="font-display font-bold text-lg text-slate-900">No encontramos ningún sticker con esos filtros</h3>
                <p class="text-xs text-slate-600">
                    Intenta cambiar los términos de búsqueda o solicita que imprimamos tu propio diseño personalizado.
                </p>
                <div class="pt-2 flex justify-center gap-3">
                    <button @click="resetFilters()"
                            class="px-4 py-2.5 text-xs font-semibold bg-slate-100 hover:bg-slate-200 rounded-xl text-slate-800 transition-colors">
                        Restablecer filtros
                    </button>
                    <button @click="customQuoteModal = true"
                            class="px-4 py-2.5 text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white rounded-xl shadow-xs transition-colors">
                        Subir mi propio logo
                    </button>
                </div>
            </div>

            <!-- Product Grid: 4 cols standard or 2 cols large -->
            <div :class="viewMode === 'grid' ? 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6' : 'grid grid-cols-1 md:grid-cols-2 gap-8'">
                <template x-for="item in filteredDesigns" :key="item.id">
                    <div class="group bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/90 hover:border-orange-400 hover:shadow-xl transition-all duration-300 flex flex-col justify-between relative">
                        
                        <!-- Image Container with Hover Zoom & Finish Overlay -->
                        <div class="relative aspect-square rounded-2xl bg-slate-50 overflow-hidden flex items-center justify-center p-4 border border-slate-100 mb-3 group-hover:bg-orange-50/30 transition-colors cursor-pointer"
                             @click="openQuickView(item)">
                            
                            <template x-if="item.image">
                                <img :src="item.image"
                                     :alt="item.name"
                                     loading="lazy"
                                     class="max-h-full max-w-full object-contain group-hover:scale-108 transition-transform duration-300 drop-shadow-md">
                            </template>
                            <template x-if="!item.image">
                                <div class="w-24 h-24 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center font-display font-black text-2xl">
                                    <span x-text="item.name.substring(0, 2).toUpperCase()"></span>
                                </div>
                            </template>

                            <!-- SKU Tag Badge -->
                            <span class="absolute top-2.5 left-2.5 text-[10px] font-mono font-bold bg-white/95 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs backdrop-blur-xs"
                                  x-text="item.sku">
                            </span>

                            <!-- Format/Size Tag -->
                            <span class="absolute bottom-2.5 right-2.5 text-[10px] font-semibold bg-slate-900/80 text-white px-2 py-0.5 rounded-md backdrop-blur-xs"
                                  x-text="item.dimensions">
                            </span>
                        </div>

                        <!-- Info details -->
                        <div class="space-y-1.5 cursor-pointer" @click="openQuickView(item)">
                            <h3 class="font-display font-bold text-sm sm:text-base text-slate-900 group-hover:text-orange-600 transition-colors line-clamp-1"
                                x-text="item.name">
                            </h3>

                            <div class="flex items-center justify-between text-xs pt-1">
                                <span class="text-slate-600 text-[11px]">Vinil 4mil • Die-Cut</span>
                                <span class="text-orange-600 font-extrabold text-sm" x-text="'$' + item.wholesalePrice + ' c/u'"></span>
                            </div>
                            <div class="text-[10px] text-slate-600 text-right">
                                tarifa de mayoreo (500 pzas)
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                            <button type="button"
                                    @click="openQuickView(item)"
                                    class="flex-1 py-2.5 px-3 text-xs font-bold text-slate-800 bg-orange-50 hover:bg-orange-600 hover:text-white rounded-xl transition-all border border-orange-200/80 hover:border-orange-600 flex items-center justify-center gap-1.5 active:scale-98">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Ver Mayoreo</span>
                            </button>
                            <button type="button"
                                    @click="orderViaWhatsApp(item, 500)"
                                    title="Pedir paquete 500 pzas directo por WhatsApp"
                                    class="p-2.5 text-emerald-700 bg-emerald-50 hover:bg-emerald-600 hover:text-white rounded-xl border border-emerald-200 transition-colors">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </main>

    <!-- CUSTOM LOGO / DESIGN UPLOAD PROMO BANNER -->
    <section class="py-16 bg-slate-950 text-white border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-orange-950/70 via-slate-900 to-amber-950/70 rounded-3xl p-8 sm:p-12 border border-orange-500/30 flex flex-col lg:flex-row items-center justify-between gap-8">
                <div class="space-y-3 max-w-2xl text-center lg:text-left">
                    <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider bg-orange-600/20 text-orange-400 rounded-full border border-orange-500/30">
                        Servicio de Impresión Personalizada
                    </span>
                    <h2 class="font-display font-extrabold text-2xl sm:text-4xl text-white tracking-tight">
                        ¿Tienes tu propio archivo o logotipo?
                    </h2>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        No te limites a nuestro catálogo. Envíanos tu diseño vectorial o imagen en alta calidad y te fabricamos el mismo lote al mayoreo con el troquelado exacto de tu silueta y acabado a elegir.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4 shrink-0 w-full lg:w-auto">
                    <button @click="customQuoteModal = true"
                            class="w-full sm:w-auto px-8 py-4 rounded-xl text-xs font-bold text-slate-950 bg-gradient-to-r from-orange-400 via-orange-300 to-amber-300 hover:from-orange-300 hover:to-amber-200 shadow-xl transition-all active:scale-95 text-center">
                        Cotizar mi propio logo
                    </button>
                    <a href="{{ route('home') }}#calculadora"
                       class="w-full sm:w-auto px-6 py-4 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-700 transition-all text-center">
                        Ver Calculadora de Precios
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- INTERACTIVE QUICK VIEW MODAL (Alpine.js) -->
    <div x-show="quickViewModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true"
         @keydown.escape.window="quickViewModal = false">
        
        <div class="min-h-screen px-4 text-center flex items-center justify-center py-8">
            <!-- Backdrop -->
            <div x-show="quickViewModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="quickViewModal = false"
                 class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity"></div>

            <!-- Modal Panel -->
            <div x-show="quickViewModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative inline-block w-full max-w-3xl p-6 sm:p-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-orange-200 my-8">
                
                <!-- Close Button -->
                <button @click="quickViewModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-2 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center" x-if="activeItem">
                    <!-- Left: Large Image Stage -->
                    <div class="md:col-span-5 bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-6 border border-orange-100 flex items-center justify-center relative aspect-square">
                        <template x-if="activeItem && activeItem.image">
                            <img :src="activeItem.image" :alt="activeItem.name" class="max-h-60 max-w-60 object-contain drop-shadow-xl">
                        </template>
                        <template x-if="activeItem && !activeItem.image">
                            <div class="w-32 h-32 rounded-2xl bg-orange-200 text-orange-700 flex items-center justify-center font-display font-black text-3xl">
                                <span x-text="activeItem.name.substring(0, 2)"></span>
                            </div>
                        </template>

                        <span class="absolute top-3 left-3 text-[11px] font-mono font-bold bg-white text-slate-800 px-2 py-0.5 rounded shadow-2xs border border-slate-200"
                              x-text="activeItem ? activeItem.sku : ''"></span>
                    </div>

                    <!-- Right: Specs & Wholesale Tier Matrix -->
                    <div class="md:col-span-7 space-y-4">
                        <div>
                            <span class="text-[11px] font-bold text-orange-600 uppercase tracking-wider">Vinil 4mil • Troquelado Die-Cut</span>
                            <h2 class="font-display font-extrabold text-2xl text-slate-900 mt-1" x-text="activeItem ? activeItem.name : ''"></h2>
                            <p class="text-xs text-slate-600 mt-1.5 leading-relaxed" x-text="activeItem ? activeItem.description : ''"></p>
                        </div>

                        <!-- Technical specs chips -->
                        <div class="flex flex-wrap gap-2 text-xs">
                            <div class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-medium">
                                Medida: <strong x-text="activeItem ? activeItem.dimensions : ''"></strong>
                            </div>
                            <div class="px-2.5 py-1 rounded-lg bg-orange-100 text-orange-800 font-medium">
                                Acabados: <strong>Tornasol, Mate o Brillante</strong>
                            </div>
                        </div>

                        <!-- Wholesale Pricing Table for this item -->
                        <div class="pt-2">
                            <div class="text-xs font-bold text-slate-900 mb-2 flex items-center justify-between">
                                <span>Escala de Precios Mayoristas:</span>
                                <span class="text-emerald-700 text-[11px] font-bold">¡Hasta 68% off!</span>
                            </div>

                            <div class="space-y-1.5 text-xs">
                                <div class="flex justify-between items-center p-2 rounded-xl bg-slate-50 border border-slate-200/70">
                                    <span class="font-medium text-slate-700">50 piezas</span>
                                    <span class="font-bold text-slate-900">$6.50 c/u <span class="text-slate-400 font-normal">($325 total)</span></span>
                                </div>
                                <div class="flex justify-between items-center p-2 rounded-xl bg-slate-50 border border-slate-200/70">
                                    <span class="font-medium text-slate-700">100 piezas</span>
                                    <span class="font-bold text-slate-900">$5.20 c/u <span class="text-slate-400 font-normal">($520 total)</span></span>
                                </div>
                                <div class="flex justify-between items-center p-2 rounded-xl bg-slate-50 border border-slate-200/70">
                                    <span class="font-medium text-slate-700">250 piezas</span>
                                    <span class="font-bold text-slate-900">$4.10 c/u <span class="text-slate-400 font-normal">($1,025 total)</span></span>
                                </div>
                                <div class="flex justify-between items-center p-2 rounded-xl bg-orange-50 border border-orange-200 font-bold text-orange-950">
                                    <span class="flex items-center gap-1.5">
                                        <span>500 piezas</span>
                                        <span class="text-[10px] bg-orange-600 text-white px-1.5 py-0.2 rounded">Recomendado</span>
                                    </span>
                                    <span class="text-orange-600 font-extrabold text-sm">$2.40 c/u <span class="text-xs font-normal text-slate-600">($1,200 total)</span></span>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Actions -->
                        <div class="pt-3 space-y-2">
                            <button type="button"
                                    @click="orderViaWhatsApp(activeItem, 500)"
                                    class="w-full py-3.5 px-4 text-xs font-bold text-slate-950 bg-gradient-to-r from-orange-400 via-orange-300 to-amber-300 hover:from-orange-300 hover:to-amber-200 rounded-xl shadow-md transition-all active:scale-98 flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Pedir Paquete 500 pzas por WhatsApp</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CUSTOM LOGO / DESIGN MODAL (Alpine.js) -->
    <div x-show="customQuoteModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true"
         @keydown.escape.window="customQuoteModal = false">
        
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <!-- Backdrop -->
            <div x-show="customQuoteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="customQuoteModal = false"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <!-- Modal Panel -->
            <div x-show="customQuoteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative inline-block w-full max-w-lg p-6 sm:p-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-orange-200 my-8">
                
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-display font-bold text-base text-slate-900">Cotizar Mi Logo / Diseño Personalizado</h3>
                        <p class="text-[11px] text-slate-500">Muestra digital de corte previa incluida</p>
                    </div>
                    <button @click="customQuoteModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form class="space-y-4 mt-4" onsubmit="event.preventDefault();">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nombre o Marca *</label>
                        <input type="text" id="customName" placeholder="Tu nombre o empresa" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/50">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tiraje Deseado *</label>
                            <select id="customQty" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/50">
                                <option value="50">50 piezas (Mínimo)</option>
                                <option value="100">100 piezas</option>
                                <option value="250">250 piezas</option>
                                <option value="500" selected>500 piezas (Recomendado)</option>
                                <option value="1000">1,000 piezas</option>
                                <option value="2500">2,500+ piezas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Acabado *</label>
                            <select id="customFinish" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/50">
                                <option value="Tornasol Prisma">Tornasol Prisma ✨</option>
                                <option value="Brillante High-Gloss">Brillante High-Gloss</option>
                                <option value="Mate Velvet">Mate Velvet</option>
                                <option value="Metálico Plata">Metálico Plata/Oro</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Enlace a tu archivo o detalles del logo</label>
                        <input type="text" id="customDetails" placeholder="Link de Drive / Dropbox o dimensiones deseadas" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/50">
                    </div>

                    <div class="pt-2">
                        <button type="button"
                                @click="
                                    let name = document.getElementById('customName').value || 'Cliente';
                                    let qty = document.getElementById('customQty').value;
                                    let finish = document.getElementById('customFinish').value;
                                    let details = document.getElementById('customDetails').value || 'Sin archivo adjunto';
                                    let msg = 'Hola Luna Stickers! Mi nombre es ' + name + ' y quiero cotizar ' + qty + ' stickers personalizados con mi propio logo en acabado ' + finish + '. Detalles: ' + details;
                                    window.open('https://wa.me/5215500000000?text=' + encodeURIComponent(msg), '_blank');
                                    customQuoteModal = false;
                                "
                                class="w-full py-3.5 px-4 text-xs font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 rounded-xl shadow-md transition-all active:scale-98 flex items-center justify-center gap-2">
                            <span>Solicitar Muestra Digital por WhatsApp</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-12 text-xs border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-white font-display font-bold text-base">
                <div class="w-6 h-6 rounded-md bg-orange-600 flex items-center justify-center text-white text-xs">
                    LS
                </div>
                <span>LUNA<span class="text-orange-500">STICKERS</span></span>
                <span class="text-slate-500 font-normal text-xs ml-2">| Catálogo Mayorista</span>
            </div>
            <p>&copy; {{ date('Y') }} Luna Stickers. Producción industrial en vinil polimérico.</p>
        </div>
    </footer>

    <!-- Alpine.js Catalog Application Logic -->
    <script>
        function catalogApp() {
            return {
                mobileMenu: false,
                quickViewModal: false,
                customQuoteModal: false,
                activeItem: null,
                searchQuery: (new URLSearchParams(window.location.search)).get('search') || '',
                selectedFinish: 'all',
                selectedSize: 'all',
                sortBy: 'name_asc',
                viewMode: 'grid',
                designs: {!! $designsJson !!},

                get filteredDesigns() {
                    let list = [...this.designs];

                    // Search filter
                    if (this.searchQuery.trim() !== '') {
                        const q = this.searchQuery.toLowerCase().trim();
                        list = list.filter(item => 
                            item.name.toLowerCase().includes(q) ||
                            item.sku.toLowerCase().includes(q) ||
                            item.description.toLowerCase().includes(q)
                        );
                    }

                    // Finish filter
                    if (this.selectedFinish !== 'all') {
                        list = list.filter(item => {
                            if (this.selectedFinish === 'Tornasol') return item.finish.toLowerCase().includes('torna');
                            if (this.selectedFinish === 'Basico') return item.finish.toLowerCase().includes('basic') || item.finish.toLowerCase().includes('básic');
                            if (this.selectedFinish === 'Metálico') return item.finish.toLowerCase().includes('metál') || item.finish.toLowerCase().includes('metal');
                            if (this.selectedFinish === 'Sublimado') return item.finish.toLowerCase().includes('sublim') || item.finish.toLowerCase().includes('fantas');
                            return true;
                        });
                    }

                    // Sorting
                    if (this.sortBy === 'name_asc') {
                        list.sort((a, b) => a.name.localeCompare(b.name));
                    } else if (this.sortBy === 'name_desc') {
                        list.sort((a, b) => b.name.localeCompare(a.name));
                    } else if (this.sortBy === 'sku_asc') {
                        list.sort((a, b) => a.sku.localeCompare(b.sku));
                    }

                    return list;
                },

                openQuickView(item) {
                    this.activeItem = item;
                    this.quickViewModal = true;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.selectedFinish = 'all';
                    this.selectedSize = 'all';
                    this.sortBy = 'name_asc';
                },

                orderViaWhatsApp(item, qty) {
                    if (!item) return;
                    let msg = 'Hola Luna Stickers! Me interesa ordenar un lote mayorista de ' + qty + ' piezas del sticker ' + item.name + ' (SKU: ' + item.sku + ') en tamaño ' + item.dimensions + '. ¿Podrían apoyarme con los detalles de pago y muestra digital?';
                    window.open('https://wa.me/5215500000000?text=' + encodeURIComponent(msg), '_blank');
                }
            }
        }
    </script>
</body>
</html>
