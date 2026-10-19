<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stickers al Mayoreo | Imprenta Especializada en Vinil de Alto Rendimiento</title>
    <meta name="description"
        content="Fabricación de stickers de vinil al mayoreo para marcas, eventos y creadores. Acabados holográficos, mate, brillante y metálico. Corte die-cut exacto y precios de fábrica desde 50 piezas.">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">

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
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #0f172a;
            background-color: #fafaf9;
        }

        .font-display {
            font-family: 'Space Grotesk', system-ui, sans-serif;
        }

        .bg-grid-pattern {
            background-size: 32px 32px;
            background-image:
                linear-gradient(to right, rgba(234, 88, 12, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(234, 88, 12, 0.05) 1px, transparent 1px);
        }

        .holographic-shine {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.4) 0%, rgba(255, 200, 150, 0.6) 25%, rgba(200, 230, 255, 0.6) 50%, rgba(255, 180, 220, 0.6) 75%, rgba(255, 255, 255, 0.4) 100%);
            background-size: 200% 200%;
            animation: holoShift 6s ease infinite;
        }

        @keyframes holoShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>

<body class="min-h-screen bg-[#fafaf9] text-slate-900 selection:bg-orange-500 selection:text-white"
    x-data="{
        mobileMenu: false,
        quoteModal: false,
        calcQty: 500,
        calcSize: '7x7',
        calcFinish: 'tornasol',
        get unitPrice() {
            let base = 5.50;
            if (this.calcSize === '5x5') base = 4.20;
            if (this.calcSize === '7x7') base = 5.80;
            if (this.calcSize === '10x10') base = 7.90;
            if (this.calcSize === 'pano') base = 6.40;
    
            // Finish multiplier
            let multiplier = 1.0;
            if (this.calcFinish === 'tornasol') multiplier = 1.25;
            if (this.calcFinish === 'metalico') multiplier = 1.30;
            if (this.calcFinish === 'mate') multiplier = 1.10;
    
            // Volume discount curve
            let discount = 0;
            if (this.calcQty >= 2500) discount = 0.58;
            else if (this.calcQty >= 1000) discount = 0.50;
            else if (this.calcQty >= 500) discount = 0.42;
            else if (this.calcQty >= 250) discount = 0.30;
            else if (this.calcQty >= 100) discount = 0.18;
            else if (this.calcQty >= 50) discount = 0.05;
    
            return Math.max(1.20, (base * multiplier * (1 - discount))).toFixed(2);
        },
        get subtotal() {
            return (this.unitPrice * this.calcQty).toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        get savingsPercent() {
            if (this.calcQty >= 2500) return 68;
            if (this.calcQty >= 1000) return 60;
            if (this.calcQty >= 500) return 52;
            if (this.calcQty >= 250) return 38;
            if (this.calcQty >= 100) return 22;
            return 8;
        }
    }">

    @php
        if (!isset($featuredDesigns) || $featuredDesigns->isEmpty()) {
            $featuredDesigns = \App\Models\Design::query()
                ->active()
                ->with(['variants' => fn($q) => $q->active()->with(['stickerSize', 'stickerFinish'])])
                ->latest('id')
                ->take(8)
                ->get();
        }
    @endphp

    <x-home.navbar />

    <!-- HERO SECTION: High-impact wholesale focus -->
    <section
        class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-orange-50/50 via-white to-[#fafaf9]">
        <!-- Technical print registration marks & grid backdrop -->
        <div class="absolute inset-0 bg-grid-pattern opacity-70 pointer-events-none"></div>
        <div
            class="absolute top-8 right-8 w-16 h-16 border border-orange-200/60 rounded-full flex items-center justify-center opacity-40 pointer-events-none hidden md:flex">
            <div class="w-full h-px bg-orange-300"></div>
            <div class="h-full w-px bg-orange-300 absolute"></div>
            <div class="w-4 h-4 rounded-full border border-orange-400"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Left Column: Copy & Value Proposition -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-orange-100/90 border border-orange-300/80 text-orange-800 text-xs font-bold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-orange-600 animate-pulse"></span>
                        TALLER DE STIKERS DE CORTE DE VINIL AL MAYOREO
                    </div>

                    <h1
                        class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl tracking-tight text-slate-950 leading-[1.12]">
                        Stickers al mayoreo que hacen que tu marca se quede <span
                            class="relative whitespace-nowrap text-orange-600 inline-block">
                            pegada
                            <svg class="absolute -bottom-2 left-0 w-full h-3 text-orange-400/60" viewBox="0 0 100 12"
                                preserveAspectRatio="none" fill="currentColor">
                                <path d="M0,8 Q50,0 100,8 L100,12 Q50,4 0,12 Z"></path>
                            </svg>
                        </span> en todos lados.
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Hacemos stickers a corte de vinil de alta calidad para tu negocio o refaccionaria.
                        <strong>Variedad de colores de vinil</strong>.
                        Corte de vinil para stickers con <strong>acabados holográficos, mate, brillante y
                            metálico</strong>.
                        <span class="text-orange-700 font-semibold underline decoration-orange-300">pedidos
                            digitales</span>.
                    </p>

                    <!-- Trust indicators bullets -->
                    <div
                        class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-xs font-semibold text-slate-700 max-w-xl mx-auto lg:mx-0">
                        <div
                            class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-orange-600">✓</span>
                            <span>Vinil 100%</span>
                        </div>
                        <div
                            class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-orange-600">✓</span>
                            <span>Acabados de alta calidad</span>
                        </div>
                        <div
                            class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-orange-600">✓</span>
                            <span>Catalogo y pedidos digitales</span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                        <a href="{{ route('catalogs') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl text-base font-bold text-white bg-gradient-to-r from-orange-600 via-orange-500 to-amber-600 hover:from-orange-500 hover:to-amber-500 shadow-lg shadow-orange-600/30 hover:shadow-orange-600/40 hover:-translate-y-0.5 transition-all active:translate-y-0">
                            <span>Ver el catálogo</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Column: Interactive Product Showcase Stage -->
                <div class="lg:col-span-5 relative" x-data="{ heroFinish: 'holo' }">
                    <!-- Layered decorative card base -->
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Background glow warm amber -->
                        <div
                            class="absolute -inset-2 bg-gradient-to-r from-orange-500/20 to-amber-500/20 rounded-3xl blur-2xl opacity-60">
                        </div>

                        <div class="relative bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-orange-200/80">
                            <!-- Header of stage -->
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                    <span
                                        class="text-xs font-bold uppercase tracking-wider text-slate-800 font-display">Muestra
                                        de Acabado</span>
                                </div>
                            </div>

                            <!-- Sticker Visual Stage with dynamic finishes -->
                            <div class="relative my-6 aspect-square rounded-2xl overflow-hidden flex items-center justify-center p-6 border border-slate-100"
                                :class="{
                                    'holographic-shine bg-slate-100': heroFinish === 'holo',
                                    'bg-radial from-amber-50 to-orange-100': heroFinish === 'mate',
                                    'bg-gradient-to-tr from-slate-200 via-white to-slate-100': heroFinish === 'metalico',
                                    'bg-white shadow-inner': heroFinish === 'brillante'
                                }">

                                <!-- Simulated cut registration marks -->
                                <div class="absolute top-3 left-3 text-[9px] font-mono text-slate-400">SKU:
                                    {{ $heroSample->sku ?? 'N/A' }}</div>
                                <div class="absolute bottom-3 right-3 text-[9px] font-mono text-slate-400">VINIL BASICO
                                </div>

                                <!-- Central Sticker Simulation -->
                                <div
                                    class="relative group cursor-pointer transition-transform duration-300 hover:scale-105">
                                    @php
                                        $heroSample = $featuredDesigns->first();
                                    @endphp
                                    @if ($heroSample && $heroSample->preview_image_url)
                                        <img src="{{ asset('storage/' . $heroSample->preview_image_url) }}"
                                            alt="{{ $heroSample->name }}"
                                            class="max-h-56 max-w-56 object-contain drop-shadow-2xl">
                                    @else
                                        <!-- Fallback Graphic vector badge -->
                                        <div
                                            class="w-48 h-48 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 flex flex-col items-center justify-center text-white shadow-2xl p-4 text-center transform -rotate-3 border-4 border-white">
                                            <svg class="w-16 h-16 text-white mb-2" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polygon points="12 8 8 12 12 16 16 12 12 8"></polygon>
                                            </svg>
                                            <span class="font-display font-extrabold text-lg leading-tight">CUSTOM
                                                DIE-CUT</span>
                                            <span
                                                class="text-[10px] tracking-widest uppercase mt-1 opacity-90">WATERPROOF
                                                VINYL</span>
                                        </div>
                                    @endif

                                    <!-- Finish Overlay Tag -->
                                    <div
                                        class="absolute -bottom-3 left-1/2 -translate-x-1/2 whitespace-nowrap bg-slate-900/90 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-md backdrop-blur-xs flex items-center gap-1.5 border border-white/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                                        <span
                                            x-text="heroFinish === 'holo' ? 'VINIL BASICO' : (heroFinish === 'mate' ? 'Acabado Mate Velvet' : (heroFinish === 'metalico' ? 'Metálico Cepillado' : 'Brillante Cristalino'))"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WHOLESALE SECTORS RIBBON -->
    <section class="py-6 bg-slate-900 text-white border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-medium">
                <div class="flex items-center gap-2 text-orange-400 font-bold uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span>Especialistas en mayoreo para:</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-6 text-slate-300">
                    <span class="hover:text-orange-400 transition-colors">Refaccionarias</span>
                    <span class="text-slate-700">•</span>
                    <span class="hover:text-orange-400 transition-colors">Cafeterías & Roasters</span>
                    <span class="text-slate-700">•</span>
                    <span class="hover:text-orange-400 transition-colors">Cervecerías Artesanales</span>
                    <span class="text-slate-700">•</span>
                    <span class="hover:text-orange-400 transition-colors">Ilustradores & Creadores</span>
                    <span class="text-slate-700">•</span>
                    <span class="hover:text-orange-400 transition-colors">Agencias & Eventos</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ANATOMY OF A PREMIUM STICKER (Materials & Quality Deep Dive) -->
    <section id="acabados" class="py-20 lg:py-28 bg-[#fafaf9] border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span
                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                    Ingeniería de Materiales
                </span>
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950 mt-3 tracking-tight">
                    Por qué nuestros stickers son duraderos
                    <p class="text-slate-600 mt-3 text-base">
                        No utilizamos papel autoadhesivo frágil. Cada sticker está construido con vinil capa por capa.
                    </p>
            </div>

            <!-- Finishes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Finish 1 -->
                <div
                    class="bg-white rounded-3xl p-6 border border-orange-200/70 shadow-xs hover:border-orange-400 transition-all group">
                    <div
                        class="h-40 rounded-2xl bg-gradient-to-tr from-amber-400 via-orange-500 to-pink-500 flex items-center justify-center text-white mb-5 shadow-inner holographic-shine">
                        <span class="font-display font-black text-2xl tracking-wider drop-shadow-md">TORNASOL</span>
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-900">Tornasol</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Reflejo iridiscente que cambia de color según el ángulo de la luz. El preferido por marcas de
                        streetwear y termos.
                    </p>
                </div>

                <!-- Finish 2 -->
                <div
                    class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:border-orange-400 transition-all group">
                    <div
                        class="h-40 rounded-2xl bg-slate-900 flex items-center justify-center text-white mb-5 shadow-inner">
                        <span class="font-display font-black text-2xl tracking-wider text-slate-200">MATE</span>
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-900">Mate</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Cero reflejos o destellos. Sensación sedosa al tacto, ideal para packaging sobrio, café de
                        especialidad y arte editorial.
                    </p>
                </div>

                <!-- Finish 3 -->
                <div
                    class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:border-orange-400 transition-all group">
                    <div
                        class="h-40 rounded-2xl bg-gradient-to-tr from-orange-500 via-amber-400 to-yellow-300 flex items-center justify-center text-white mb-5 shadow-inner">
                        <span class="font-display font-black text-2xl tracking-wider text-slate-950">REFLEJANTE</span>
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-900">Reflejante</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Ideal para crear un impacto visual destacado con su superficie altamente reflectante.
                    </p>
                </div>

                <!-- Finish 4 -->
                <div
                    class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:border-orange-400 transition-all group">
                    <div
                        class="h-40 rounded-2xl bg-gradient-to-r from-slate-300 via-slate-100 to-slate-400 flex items-center justify-center text-slate-900 mb-5 shadow-inner">
                        <span class="font-display font-black text-2xl tracking-wider">METÁLICO</span>
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-900">Metálico</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Da un efecto de placa metálica y reflejos.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED CATALOG DESIGNS SHOWCASE (Real items from database) -->
    <section class="py-20 lg:py-28 bg-[#fafaf9] border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                        Modelos de Entrega Rápida
                    </span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950 mt-3 tracking-tight">
                        Destacados del Catálogo Mayorista
                    </h2>
                    <p class="text-slate-600 text-sm mt-2">
                        Diseños listos para producción inmediata en cualquiera de nuestros 4 acabados industriales.
                    </p>
                </div>
                <div>
                    <a href="{{ route('catalogs') }}"
                        class="inline-flex items-center gap-2 text-sm font-bold text-orange-600 hover:text-orange-700 hover:underline">
                        <span>Ver todos los modelos (+40 disponibles)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Sticker Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($featuredDesigns as $design)
                    <div
                        class="group bg-white rounded-3xl p-4 border border-slate-200/80 hover:border-orange-400 hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                        <!-- Image Container with Finish Background -->
                        <div
                            class="relative aspect-square rounded-2xl bg-slate-50 overflow-hidden flex items-center justify-center p-4 border border-slate-100 mb-3 group-hover:bg-orange-50/40 transition-colors">
                            @if ($design->preview_image_url)
                                <img src="{{ asset('storage/' . $design->preview_image_url) }}"
                                    alt="{{ $design->name }}" loading="lazy"
                                    class="max-h-full max-w-full object-contain group-hover:scale-108 transition-transform duration-300 drop-shadow-md">
                            @else
                                <div
                                    class="w-20 h-20 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-display font-bold text-lg">
                                    {{ Str::limit($design->name, 2, '') }}
                                </div>
                            @endif

                            <!-- SKU Tag -->
                            <span
                                class="absolute top-2.5 left-2.5 text-[10px] font-mono font-bold bg-white/90 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs backdrop-blur-xs">
                                {{ $design->sku }}
                            </span>
                        </div>

                        <!-- Card Info -->
                        <div>
                            <h3
                                class="font-display font-bold text-sm text-slate-900 group-hover:text-orange-600 transition-colors line-clamp-1">
                                {{ $design->name }}
                            </h3>
                            <div class="flex items-center justify-between text-xs mt-1 text-slate-500">
                                <span>Vinil 4mil</span>
                                <span class="font-bold text-orange-600">Desde $2.40 c/u</span>
                            </div>
                        </div>

                        <!-- Action button -->
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <a href="{{ route('catalogs') }}?search={{ urlencode($design->name) }}"
                                class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 text-xs font-bold text-slate-800 bg-slate-100 hover:bg-orange-600 hover:text-white rounded-xl transition-colors">
                                <span>Configurar Mayoreo</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-500">
                        No hay diseños registrados en este momento.
                    </div>
                @endforelse
            </div>

            <!-- Big Bottom Banner inside Showcase -->
            <div
                class="mt-12 p-8 rounded-3xl bg-gradient-to-r from-orange-600 to-amber-600 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl">
                <div>
                    <h3 class="font-display font-extrabold text-2xl">¿Tienes tu propio logotipo o diseño?</h3>
                    <p class="text-orange-100 text-sm mt-1 max-w-xl">
                        Contáctanos y te ayudamos a producir tu lote de stickers al mayoreo con tu arte personalizado,
                        sin intermediarios y con validación previa de archivo para asegurar corte perfecto.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('contact') }}"
                        class="px-6 py-3.5 bg-slate-950 hover:bg-black text-white text-xs font-bold rounded-xl shadow-md transition-all active:scale-95">
                        Contactate para tu cotización
                    </a>
                    <a href="{{ route('catalogs') }}"
                        class="px-6 py-3.5 bg-white text-orange-700 hover:bg-orange-50 text-xs font-bold rounded-xl shadow-md transition-all active:scale-95">
                        Explorar Catálogo
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- WHOLESALE ORDER STEP-BY-STEP (Frictionless process) -->
    <section id="proceso" class="py-20 lg:py-28 bg-white border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span
                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                    Flujo de Trabajo
                </span>
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950 mt-3 tracking-tight">
                    Cómo solicitar tu lote de stickers en 4 pasos
                </h2>
                <p class="text-slate-600 mt-3 text-base">
                    Sin intermediarios, sin sorpresas y con validación previa de archivo para asegurar corte perfecto.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <!-- Step 1 -->
                <div class="relative bg-orange-50/50 p-6 rounded-3xl border border-orange-100 space-y-3">
                    <span
                        class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-display font-extrabold text-base shadow-sm">
                        01
                    </span>
                    <h3 class="font-display font-bold text-base text-slate-900">Elige o Sube tu Arte</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Selecciona un diseño de nuestro catálogo mayorista a traves de nuestro sistema el cual
                        previamente tendras que pedir acceso o envía tu url para agregarlo a nuestro catalogo.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="relative bg-orange-50/50 p-6 rounded-3xl border border-orange-100 space-y-3">
                    <span
                        class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-display font-extrabold text-base shadow-sm">
                        02
                    </span>
                    <h3 class="font-display font-bold text-base text-slate-900">Muestra Digital Gratis</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Nuestro equipo vectoriza el diseño y te envía una simulación visual para tu aprobación antes de
                        entrar a máquina.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="relative bg-orange-50/50 p-6 rounded-3xl border border-orange-100 space-y-3">
                    <span
                        class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-display font-extrabold text-base shadow-sm">
                        03
                    </span>
                    <h3 class="font-display font-bold text-base text-slate-900">Preparación del pedido</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Se realiza la preparación del pedido y se confirma el pago recibido.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="relative bg-orange-50/50 p-6 rounded-3xl border border-orange-100 space-y-3">
                    <span
                        class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-display font-extrabold text-base shadow-sm">
                        04
                    </span>
                    <h3 class="font-display font-bold text-base text-slate-900">Empaque & Envío Express</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Agrupados en paquetes de 50 o 100 piezas, protegidos en caja rígida y enviados con número de
                        rastreo inmediato directo a tu puerta.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- WHOLESALE FAQ (Accordion with Alpine.js) -->
    <section id="faq" class="py-20 lg:py-24 bg-[#fafaf9] border-t border-slate-200/60" x-data="{ activeFaq: 1 }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span
                    class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                    Dudas Frecuentes
                </span>
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950 mt-3 tracking-tight">
                    Preguntas Clave sobre Compras al Mayoreo
                </h2>
            </div>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 1 ? null : 1)"
                        class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Cuál es el pedido mínimo para obtener precio de mayoreo?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200"
                            :class="activeFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="activeFaq === 1" x-cloak
                        class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Precios de mayoreo desde <strong>5 piezas por diseño</strong>, con un pedido mínimo de
                        <strong>50 piezas</strong> en total.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 2 ? null : 2)"
                        class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Puedo combinar varios diseños en un mismo pedido de mayoreo?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200"
                            :class="activeFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="activeFaq === 2" x-cloak
                        class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        ¡Sí! Puedes dividir tu pedido en múltiplos de 5 piezas por diseño (por ejemplo, 100 piezas
                        divididas en 20 diseños diferentes de 5 piezas cada uno). Así puedes surtir varios modelos para
                        tu negocio al mismo tiempo.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 3 ? null : 3)"
                        class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Qué tan resistentes son al agua e intemperie?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200"
                            :class="activeFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="activeFaq === 3" x-cloak
                        class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Alta resistencia: impresos en vinil de rotulación que soporta lluvia, lavado a presión y sol
                        directo por más de 2 años sin decolorarse.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 4 ? null : 4)"
                        class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Qué formato de archivo debo proporcionar para mis stickers?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200"
                            :class="activeFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="activeFaq === 4" x-cloak
                        class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Aceptamos archivos vectoriales en <strong>AI, PDF, SVG o EPS</strong>o con un url de una imagen.
                        Si tu archivo necesita ajustes o trazado de línea de corte, nuestro equipo de diseño te apoya
                        sin costo adicional.
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 5 ? null : 5)"
                        class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Emiten factura fiscal para empresas y negocios?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200"
                            :class="activeFaq === 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                    <div x-show="activeFaq === 5" x-cloak
                        class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Sí, todos nuestros precios pueden facturarse con CFDI 4.0 oficial (+ IVA). Al concretar tu
                        pedido solo proporciónanos tu Constancia de Situación Fiscal y Uso de CFDI.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FINAL CALL TO ACTION -->
    <section class="py-20 bg-slate-950 text-white relative overflow-hidden">
        <!-- Orange atmospheric glow -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-orange-950/80 via-slate-950 to-amber-950/70 pointer-events-none">
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-600/20 text-orange-400 border border-orange-500/30">
                Obten tu lote de stickers al mayoreo
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
                Precios de mayoreo con Two Brothers Stickers & Designs
            </h2>
            <p class="text-slate-300 max-w-2xl mx-auto text-base leading-relaxed">
                Precios de mayoreo desde 5 piezas por diseño, con un pedido mínimo de 50 piezas en total.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="{{ route('catalogs') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 shadow-xl shadow-orange-600/30 active:scale-95 transition-all">
                    <span>Explorar el catálogo</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
                <a href="{{ route('contact') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-sm font-bold text-slate-200 bg-slate-900 hover:bg-slate-800 border border-slate-700 transition-all">
                    <span>Contacto</span>
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <x-home.footer />


</body>

</html>
