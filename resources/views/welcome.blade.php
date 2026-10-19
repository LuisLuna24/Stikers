<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stickers al Mayoreo | Imprenta Especializada en Vinil de Alto Rendimiento</title>
    <meta name="description" content="Fabricación de stickers de vinil al mayoreo para marcas, eventos y creadores. Acabados holográficos, mate, brillante y metálico. Corte die-cut exacto y precios de fábrica desde 50 piezas.">

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
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(234, 88, 12, 0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(234, 88, 12, 0.05) 1px, transparent 1px);
        }
        .holographic-shine {
            background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,200,150,0.6) 25%, rgba(200,230,255,0.6) 50%, rgba(255,180,220,0.6) 75%, rgba(255,255,255,0.4) 100%);
            background-size: 200% 200%;
            animation: holoShift 6s ease infinite;
        }
        @keyframes holoShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
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
                ->with(['variants' => fn ($q) => $q->active()->with(['stickerSize', 'stickerFinish'])])
                ->latest('id')
                ->take(8)
                ->get();
        }
    @endphp

    <!-- Top Announcement Strip -->
    <div class="bg-gradient-to-r from-orange-950 via-orange-900 to-amber-950 text-orange-100 text-xs py-2 px-4 border-b border-orange-800/40">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
            <div class="flex items-center gap-2 tracking-wide font-medium">
                <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded bg-orange-500/20 text-orange-300 font-bold text-[10px] uppercase border border-orange-400/30">Mayoreo Directo</span>
                <span>Tirajes desde 50 pzas con hasta 68% de descuento por volumen</span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-orange-200/80">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    Muestra digital previa en &lt; 2 hrs
                </span>
                <span class="hidden md:inline-block text-orange-600">•</span>
                <span class="hidden md:flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"></path></svg>
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
                    <a href="{{ route('home') }}" class="text-orange-600 font-semibold relative after:absolute after:bottom-[-6px] after:left-0 after:right-0 after:h-0.5 after:bg-orange-600 after:rounded-full">
                        Inicio
                    </a>
                    <a href="{{ route('catalogs') }}" class="hover:text-orange-600 transition-colors flex items-center gap-1.5">
                        <span>Catálogo</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded-full border border-orange-200">
                            40+ Modelos
                        </span>
                    </a>
                    <a href="#calculadora" class="hover:text-orange-600 transition-colors">
                        Calculadora
                    </a>
                    <a href="#acabados" class="hover:text-orange-600 transition-colors">
                        Acabados
                    </a>
                    <a href="#proceso" class="hover:text-orange-600 transition-colors">
                        Cómo Pedir
                    </a>
                    <a href="#faq" class="hover:text-orange-600 transition-colors">
                        Preguntas
                    </a>
                </nav>

                <!-- Actions / CTAs -->
                <div class="hidden sm:flex items-center gap-3">
                    <button @click="quoteModal = true"
                            class="px-4 py-2.5 text-xs font-semibold text-orange-800 bg-orange-50 hover:bg-orange-100 rounded-xl transition-all border border-orange-200/90 active:scale-95">
                        Cotizar a Medida
                    </button>
                    <a href="{{ route('catalogs') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 rounded-xl shadow-md shadow-orange-600/25 transition-all active:scale-95">
                        <span>Ver Catálogo</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="p-2.5 text-slate-600 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-colors" title="Mi Panel">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="p-2.5 text-slate-600 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-colors text-xs font-semibold" title="Iniciar sesión">
                                Entrar
                            </a>
                        @endauth
                    @endif
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
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden border-b border-orange-100 bg-white/98 px-5 pt-3 pb-6 space-y-3 shadow-xl">
            <a href="{{ route('home') }}" @click="mobileMenu = false" class="block py-2 font-semibold text-orange-600 border-l-2 border-orange-600 pl-3">
                Inicio
            </a>
            <a href="{{ route('catalogs') }}" @click="mobileMenu = false" class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
                Catálogo de Stickers (40+ Modelos)
            </a>
            <a href="#calculadora" @click="mobileMenu = false" class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
                Calculadora de Mayoreo
            </a>
            <a href="#acabados" @click="mobileMenu = false" class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
                Materiales & Acabados
            </a>
            <a href="#proceso" @click="mobileMenu = false" class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
                Cómo Funciona el Pedido
            </a>
            <a href="#faq" @click="mobileMenu = false" class="block py-2 text-slate-700 font-medium pl-3 hover:text-orange-600">
                Preguntas Frecuentes
            </a>
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('catalogs') }}" class="w-full text-center py-3 text-sm font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 rounded-xl">
                    Explorar Todo el Catálogo
                </a>
                <button @click="quoteModal = true; mobileMenu = false" class="w-full text-center py-2.5 text-sm font-semibold text-orange-800 bg-orange-50 rounded-xl border border-orange-200">
                    Cotizar Proyecto Especial
                </button>
            </div>
        </div>
    </header>

    <!-- HERO SECTION: High-impact wholesale focus -->
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-orange-50/50 via-white to-[#fafaf9]">
        <!-- Technical print registration marks & grid backdrop -->
        <div class="absolute inset-0 bg-grid-pattern opacity-70 pointer-events-none"></div>
        <div class="absolute top-8 right-8 w-16 h-16 border border-orange-200/60 rounded-full flex items-center justify-center opacity-40 pointer-events-none hidden md:flex">
            <div class="w-full h-px bg-orange-300"></div>
            <div class="h-full w-px bg-orange-300 absolute"></div>
            <div class="w-4 h-4 rounded-full border border-orange-400"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- Left Column: Copy & Value Proposition -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-orange-100/90 border border-orange-300/80 text-orange-800 text-xs font-bold tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-orange-600 animate-pulse"></span>
                        TALLER INDUSTRIAL DE STICKERS EN VINIL
                    </div>

                    <h1 class="font-display font-extrabold text-4xl sm:text-5xl lg:text-6xl tracking-tight text-slate-950 leading-[1.12]">
                        Stickers al mayoreo que hacen que tu marca se quede <span class="relative whitespace-nowrap text-orange-600 inline-block">
                            pegada
                            <svg class="absolute -bottom-2 left-0 w-full h-3 text-orange-400/60" viewBox="0 0 100 12" preserveAspectRatio="none" fill="currentColor">
                                <path d="M0,8 Q50,0 100,8 L100,12 Q50,4 0,12 Z"></path>
                            </svg>
                        </span> en todos lados.
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Imprimimos vinil polimérico 4mil de alta tenacidad con laminado UV. 
                        <strong>100% resistente al agua, sol y rayones</strong>. 
                        Corte die-cut milimétrico a la silueta de tu logo y precios de mayoreo escalonados 
                        <span class="text-orange-700 font-semibold underline decoration-orange-300">desde 50 piezas</span>.
                    </p>

                    <!-- Trust indicators bullets -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-2 text-xs font-semibold text-slate-700 max-w-xl mx-auto lg:mx-0">
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-orange-600">✓</span>
                            <span>Vinil 100% impermeable</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-orange-600">✓</span>
                            <span>Troquelado láser exacto</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-orange-600">✓</span>
                            <span>Muestra previa sin costo</span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                        <a href="{{ route('catalogs') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl text-base font-bold text-white bg-gradient-to-r from-orange-600 via-orange-500 to-amber-600 hover:from-orange-500 hover:to-amber-500 shadow-lg shadow-orange-600/30 hover:shadow-orange-600/40 hover:-translate-y-0.5 transition-all active:translate-y-0">
                            <span>Ver Catálogo Mayorista</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="#calculadora"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl text-sm font-bold text-slate-800 bg-white hover:bg-orange-50/80 border border-slate-300/80 hover:border-orange-300 shadow-xs transition-all">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span>Calculadora de Precios</span>
                        </a>
                    </div>

                    <!-- Client Metric & Social Proof -->
                    <div class="pt-6 border-t border-slate-200/70 flex flex-wrap items-center justify-center lg:justify-start gap-6 text-xs text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <div class="flex text-amber-500">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <span class="font-bold text-slate-800">4.9/5</span>
                            <span>en +14,000 pedidos</span>
                        </div>
                        <span class="hidden sm:inline text-slate-300">•</span>
                        <div>Garantía de reposición ante cualquier defecto</div>
                    </div>
                </div>

                <!-- Right Column: Interactive Product Showcase Stage -->
                <div class="lg:col-span-5 relative" x-data="{ heroFinish: 'holo' }">
                    <!-- Layered decorative card base -->
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <!-- Background glow warm amber -->
                        <div class="absolute -inset-2 bg-gradient-to-r from-orange-500/20 to-amber-500/20 rounded-3xl blur-2xl opacity-60"></div>

                        <div class="relative bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-orange-200/80">
                            <!-- Header of stage -->
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-800 font-display">Muestra de Acabado</span>
                                </div>
                                <span class="px-2.5 py-1 text-[11px] font-bold text-orange-700 bg-orange-50 border border-orange-200 rounded-md">
                                    Calidad 1440 DPI
                                </span>
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
                                <div class="absolute top-3 left-3 text-[9px] font-mono text-slate-400">CUT: DIE-CUT 0.1mm</div>
                                <div class="absolute bottom-3 right-3 text-[9px] font-mono text-slate-400">VINYL 4MIL HD</div>

                                <!-- Central Sticker Simulation -->
                                <div class="relative group cursor-pointer transition-transform duration-300 hover:scale-105">
                                    @php
                                        $heroSample = $featuredDesigns->first();
                                    @endphp
                                    @if ($heroSample && $heroSample->preview_image_url)
                                        <img src="{{ asset('storage/' . $heroSample->preview_image_url) }}"
                                             alt="{{ $heroSample->name }}"
                                             class="max-h-56 max-w-56 object-contain drop-shadow-2xl">
                                    @else
                                        <!-- Fallback Graphic vector badge -->
                                        <div class="w-48 h-48 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 flex flex-col items-center justify-center text-white shadow-2xl p-4 text-center transform -rotate-3 border-4 border-white">
                                            <svg class="w-16 h-16 text-white mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polygon points="12 8 8 12 12 16 16 12 12 8"></polygon></svg>
                                            <span class="font-display font-extrabold text-lg leading-tight">CUSTOM DIE-CUT</span>
                                            <span class="text-[10px] tracking-widest uppercase mt-1 opacity-90">WATERPROOF VINYL</span>
                                        </div>
                                    @endif

                                    <!-- Finish Overlay Tag -->
                                    <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 whitespace-nowrap bg-slate-900/90 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-md backdrop-blur-xs flex items-center gap-1.5 border border-white/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                                        <span x-text="heroFinish === 'holo' ? 'Acabado Tornasol Prisma' : (heroFinish === 'mate' ? 'Acabado Mate Velvet' : (heroFinish === 'metalico' ? 'Metálico Cepillado' : 'Brillante Cristalino'))"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Finish Switcher Controls -->
                            <div class="space-y-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Prueba el efecto de acabado:</span>
                                <div class="grid grid-cols-4 gap-2 text-xs">
                                    <button @click="heroFinish = 'holo'"
                                            :class="heroFinish === 'holo' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-orange-50 border-slate-200'"
                                            class="py-2 px-1 text-center rounded-xl border transition-all text-[11px]">
                                        Tornasol
                                    </button>
                                    <button @click="heroFinish = 'mate'"
                                            :class="heroFinish === 'mate' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-orange-50 border-slate-200'"
                                            class="py-2 px-1 text-center rounded-xl border transition-all text-[11px]">
                                        Mate
                                    </button>
                                    <button @click="heroFinish = 'brillante'"
                                            :class="heroFinish === 'brillante' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-orange-50 border-slate-200'"
                                            class="py-2 px-1 text-center rounded-xl border transition-all text-[11px]">
                                        Brillante
                                    </button>
                                    <button @click="heroFinish = 'metalico'"
                                            :class="heroFinish === 'metalico' ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-orange-50 border-slate-200'"
                                            class="py-2 px-1 text-center rounded-xl border transition-all text-[11px]">
                                        Metálico
                                    </button>
                                </div>
                            </div>

                            <!-- Mini wholesale package highlight pill -->
                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between bg-orange-50/80 p-3.5 rounded-xl border border-orange-200/60">
                                <div>
                                    <div class="text-[11px] font-bold text-orange-950 uppercase tracking-tight">Paquete 500 pzas recomendado</div>
                                    <div class="text-xs text-orange-800">Costo unitario mayorista</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-display font-black text-xl text-orange-600 leading-none">$2.40 <span class="text-xs font-normal text-slate-600">c/u</span></div>
                                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded">Ahorras 58%</span>
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
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                    <span>Especialistas en mayoreo para:</span>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-6 text-slate-300">
                    <span class="hover:text-orange-400 transition-colors">Marcas de Ropa & Streetwear</span>
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

    <!-- INTERACTIVE WHOLESALE PRICING CALCULATOR -->
    <section id="calculadora" class="py-20 lg:py-28 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                    Escala de Descuento Transparente
                </span>
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950 mt-3 tracking-tight">
                    Calculadora de Mayoreo en Tiempo Real
                </h2>
                <p class="text-slate-600 mt-3 text-base">
                    Entre mayor sea tu lote, menor es el costo por sticker. Ajusta la cantidad, tamaño y acabado para ver tu ahorro inmediato.
                </p>
            </div>

            <div class="max-w-5xl mx-auto bg-gradient-to-br from-orange-50/60 via-white to-amber-50/40 rounded-3xl p-6 sm:p-10 border border-orange-200/80 shadow-lg">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Controls Column -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Step 1: Select Quantity -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-orange-600 text-white text-xs flex items-center justify-center font-bold">1</span>
                                    Cantidad de Stickers
                                </label>
                                <span class="font-display font-extrabold text-lg text-orange-600" x-text="calcQty + ' piezas'"></span>
                            </div>
                            <!-- Preset Buttons -->
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                                <template x-for="qty in [50, 100, 250, 500, 1000, 2500]">
                                    <button type="button"
                                            @click="calcQty = qty"
                                            :class="calcQty === qty ? 'bg-orange-600 text-white font-bold border-orange-600 shadow-sm scale-102' : 'bg-white text-slate-700 hover:bg-orange-50 border-slate-200'"
                                            class="py-2.5 px-2 rounded-xl text-xs font-semibold border transition-all text-center">
                                        <span x-text="qty"></span> pzas
                                    </button>
                                </template>
                            </div>
                            <input type="range" min="50" max="2500" step="50" x-model.number="calcQty"
                                   class="w-full mt-4 accent-orange-600 cursor-pointer">
                            <div class="flex justify-between text-[11px] text-slate-600 mt-1">
                                <span>50 pzas (Mínimo)</span>
                                <span>500 pzas (Recomendado)</span>
                                <span>2,500+ pzas</span>
                            </div>
                        </div>

                        <!-- Step 2: Select Size -->
                        <div>
                            <label class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-2">
                                <span class="w-5 h-5 rounded-full bg-orange-600 text-white text-xs flex items-center justify-center font-bold">2</span>
                                Tamaño sugerido
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button type="button" @click="calcSize = '5x5'"
                                        :class="calcSize === '5x5' ? 'border-orange-600 bg-orange-50/90 text-orange-950 font-bold' : 'border-slate-200 bg-white text-slate-700'"
                                        class="p-3 rounded-xl border text-left transition-all">
                                    <div class="font-bold text-xs">5 x 5 cm</div>
                                    <div class="text-[11px] text-slate-600">Ideal termos / cel</div>
                                </button>
                                <button type="button" @click="calcSize = '7x7'"
                                        :class="calcSize === '7x7' ? 'border-orange-600 bg-orange-50/90 text-orange-950 font-bold' : 'border-slate-200 bg-white text-slate-700'"
                                        class="p-3 rounded-xl border text-left transition-all">
                                    <div class="font-bold text-xs">7 x 7 cm</div>
                                    <div class="text-[11px] text-orange-700 font-semibold">Estándar TOP</div>
                                </button>
                                <button type="button" @click="calcSize = '10x10'"
                                        :class="calcSize === '10x10' ? 'border-orange-600 bg-orange-50/90 text-orange-950 font-bold' : 'border-slate-200 bg-white text-slate-700'"
                                        class="p-3 rounded-xl border text-left transition-all">
                                    <div class="font-bold text-xs">10 x 10 cm</div>
                                    <div class="text-[11px] text-slate-600">Gran impacto / auto</div>
                                </button>
                                <button type="button" @click="calcSize = 'pano'"
                                        :class="calcSize === 'pano' ? 'border-orange-600 bg-orange-50/90 text-orange-950 font-bold' : 'border-slate-200 bg-white text-slate-700'"
                                        class="p-3 rounded-xl border text-left transition-all">
                                    <div class="font-bold text-xs">15 x 3.5 cm</div>
                                    <div class="text-[11px] text-slate-600">Panorámico</div>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Finish -->
                        <div>
                            <label class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-2">
                                <span class="w-5 h-5 rounded-full bg-orange-600 text-white text-xs flex items-center justify-center font-bold">3</span>
                                Tipo de Acabado
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <button type="button" @click="calcFinish = 'brillante'"
                                        :class="calcFinish === 'brillante' ? 'border-orange-600 bg-orange-50 text-orange-950 font-bold ring-1 ring-orange-500' : 'border-slate-200 bg-white text-slate-700'"
                                        class="py-2.5 px-3 rounded-xl border text-xs text-center transition-all">
                                    Brillante High-Gloss
                                </button>
                                <button type="button" @click="calcFinish = 'mate'"
                                        :class="calcFinish === 'mate' ? 'border-orange-600 bg-orange-50 text-orange-950 font-bold ring-1 ring-orange-500' : 'border-slate-200 bg-white text-slate-700'"
                                        class="py-2.5 px-3 rounded-xl border text-xs text-center transition-all">
                                    Mate Velvet
                                </button>
                                <button type="button" @click="calcFinish = 'tornasol'"
                                        :class="calcFinish === 'tornasol' ? 'border-orange-600 bg-orange-50 text-orange-950 font-bold ring-1 ring-orange-500' : 'border-slate-200 bg-white text-slate-700'"
                                        class="py-2.5 px-3 rounded-xl border text-xs text-center transition-all">
                                    Tornasol Prisma ✨
                                </button>
                                <button type="button" @click="calcFinish = 'metalico'"
                                        :class="calcFinish === 'metalico' ? 'border-orange-600 bg-orange-50 text-orange-950 font-bold ring-1 ring-orange-500' : 'border-slate-200 bg-white text-slate-700'"
                                        class="py-2.5 px-3 rounded-xl border text-xs text-center transition-all">
                                    Metálico Plata
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Live Calculation Summary Card -->
                    <div class="lg:col-span-5">
                        <div class="bg-slate-950 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-orange-500/30 relative overflow-hidden">
                            <!-- Background accent glow -->
                            <div class="absolute top-0 right-0 w-36 h-36 bg-orange-600/20 rounded-full blur-3xl pointer-events-none"></div>

                            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                                <span class="text-xs font-bold uppercase tracking-wider text-orange-400 font-display">Resumen de Cotización</span>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    ¡Ahorras <span x-text="savingsPercent"></span>%!
                                </span>
                            </div>

                            <div class="py-6 space-y-4">
                                <div class="flex justify-between items-baseline">
                                    <span class="text-sm text-slate-400">Precio unitario:</span>
                                    <div class="text-right">
                                        <span class="font-display font-extrabold text-3xl text-white" x-text="'$' + unitPrice"></span>
                                        <span class="text-xs text-slate-400">MXN / pza</span>
                                    </div>
                                </div>

                                <div class="flex justify-between text-xs text-slate-300 pt-2 border-t border-slate-900">
                                    <span>Tiraje total:</span>
                                    <span class="font-bold text-white" x-text="calcQty + ' unidades'"></span>
                                </div>

                                <div class="flex justify-between text-xs text-slate-300">
                                    <span>Troquelado:</span>
                                    <span class="font-bold text-orange-300">Die-cut a la forma (Incluido)</span>
                                </div>

                                <div class="flex justify-between text-xs text-slate-300">
                                    <span>Tiempo de producción:</span>
                                    <span class="font-bold text-white">48 a 72 hrs hábiles</span>
                                </div>

                                <div class="pt-4 border-t border-slate-800 flex justify-between items-baseline">
                                    <span class="text-base font-bold text-slate-200">Inversión Estimada:</span>
                                    <div class="text-right">
                                        <span class="font-display font-black text-2xl text-orange-400" x-text="'$' + subtotal"></span>
                                        <span class="text-[10px] text-slate-400 block">+ IVA si requieres factura</span>
                                    </div>
                                </div>
                            </div>

                            <!-- CTA Buttons inside summary -->
                            <div class="space-y-3 pt-2">
                                <a :href="'https://wa.me/5215500000000?text=' + encodeURIComponent('Hola! Me interesa cotizar un lote mayorista de ' + calcQty + ' stickers tamaño ' + calcSize + ' en acabado ' + calcFinish + '. ¿Podrían apoyarme con la muestra digital?')"
                                   target="_blank"
                                   class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl text-xs font-bold text-slate-950 bg-gradient-to-r from-orange-400 via-orange-300 to-amber-300 hover:from-orange-300 hover:to-amber-200 transition-all shadow-md active:scale-98">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                    <span>Pedir este lote por WhatsApp</span>
                                </a>
                                <a href="{{ route('catalogs') }}"
                                   class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-800 transition-all">
                                    <span>Elegir diseños en el Catálogo</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ANATOMY OF A PREMIUM STICKER (Materials & Quality Deep Dive) -->
    <section id="acabados" class="py-20 lg:py-28 bg-[#fafaf9] border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                    Ingeniería de Materiales
                </span>
                <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950 mt-3 tracking-tight">
                    Por qué nuestros stickers duran años sin despegarse ni decolorarse
                </h2>
                <p class="text-slate-600 mt-3 text-base">
                    No utilizamos papel autoadhesivo frágil. Cada sticker está construido con vinil polimérico automotriz de 5 capas diseñado para uso rudo.
                </p>
            </div>

            <!-- 5 Layers Visual Architecture Breakdown -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-16">
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">Capa 1</span>
                    <h3 class="font-display font-bold text-sm text-slate-900">Laminado UV Pro</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Escudo contra rayos de sol, rayaduras constantes y químicos de limpieza.</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">Capa 2</span>
                    <h3 class="font-display font-bold text-sm text-slate-900">Tinta Eco-Solvente</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Pigmentos de alta densidad fotográfica (1440 DPI) que no se borran con el agua.</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">Capa 3</span>
                    <h3 class="font-display font-bold text-sm text-slate-900">Vinil Polimérico 4mil</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Cuerpo flexible y grueso de alta tenacidad que no se desgarra al estirarse.</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">Capa 4</span>
                    <h3 class="font-display font-bold text-sm text-slate-900">Adhesivo Solvente</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Agarre firme en metal, plástico, cristal o madera sin dejar residuo pegajoso.</p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-2">
                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold bg-orange-100 text-orange-700 rounded">Capa 5</span>
                    <h3 class="font-display font-bold text-sm text-slate-900">Liner Siliconado</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">Respaldo grueso que facilita el despegue rápido en eventos y mostrador.</p>
                </div>
            </div>

            <!-- Finishes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Finish 1 -->
                <div class="bg-white rounded-3xl p-6 border border-orange-200/70 shadow-xs hover:border-orange-400 transition-all group">
                    <div class="h-40 rounded-2xl bg-gradient-to-tr from-amber-400 via-orange-500 to-pink-500 flex items-center justify-center text-white mb-5 shadow-inner holographic-shine">
                        <span class="font-display font-black text-2xl tracking-wider drop-shadow-md">TORNASOL</span>
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-900">Tornasol Prisma</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Reflejo iridiscente que cambia de color según el ángulo de la luz. El preferido por marcas de streetwear y termos.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-orange-700 flex items-center justify-between">
                        <span>Recomendado: Logos atrevidos</span>
                        <span>+25% valor</span>
                    </div>
                </div>

                <!-- Finish 2 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:border-orange-400 transition-all group">
                    <div class="h-40 rounded-2xl bg-slate-900 flex items-center justify-center text-white mb-5 shadow-inner">
                        <span class="font-display font-black text-2xl tracking-wider text-slate-200">MATE VELVET</span>
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-900">Mate Aterciopelado</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Cero reflejos o destellos. Sensación sedosa al tacto, ideal para packaging sobrio, café de especialidad y arte editorial.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-orange-700 flex items-center justify-between">
                        <span>Recomendado: Marcas premium</span>
                        <span>Acabado elegante</span>
                    </div>
                </div>

                <!-- Finish 3 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:border-orange-400 transition-all group">
                    <div class="h-40 rounded-2xl bg-gradient-to-tr from-orange-500 via-amber-400 to-yellow-300 flex items-center justify-center text-white mb-5 shadow-inner">
                        <span class="font-display font-black text-2xl tracking-wider text-slate-950">BRILLANTE</span>
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-900">Brillante High-Gloss</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Máxima saturación de color y contraste fotográfico profundo. Altamente reflectante y resistente a grasa y lavavajillas.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-orange-700 flex items-center justify-between">
                        <span>Recomendado: Ilustraciones vivas</span>
                        <span>El más popular</span>
                    </div>
                </div>

                <!-- Finish 4 -->
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:border-orange-400 transition-all group">
                    <div class="h-40 rounded-2xl bg-gradient-to-r from-slate-300 via-slate-100 to-slate-400 flex items-center justify-center text-slate-900 mb-5 shadow-inner">
                        <span class="font-display font-black text-2xl tracking-wider">METÁLICO</span>
                    </div>
                    <h3 class="font-display font-bold text-lg text-slate-900">Metálico Plata & Oro</h3>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Base reflectante plateada o dorada cepillada. Da un efecto de placa metálica industrial o distintivo de certificación.
                    </p>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-orange-700 flex items-center justify-between">
                        <span>Recomendado: Insignias de marca</span>
                        <span>Aspecto joya</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CUTTING & DIE-CUT FORMATS -->
    <section class="py-20 lg:py-24 bg-white border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-5 space-y-5">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
                        Corte de Precisión
                    </span>
                    <h2 class="font-display font-extrabold text-3xl sm:text-4xl text-slate-950 tracking-tight">
                        Cualquier forma que imagines, cortada al milímetro
                    </h2>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Nuestras cuchillas controladas por computadora generan un corte limpio sin rebabas ni bordes blancos desfasados. 
                        Tú decides la presentación ideal para tu negocio.
                    </p>

                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-orange-50/60 border border-orange-100">
                            <div class="w-8 h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                01
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-sm text-slate-900">Die Cut (Corte a la silueta individual)</h3>
                                <p class="text-xs text-slate-600 mt-0.5">El vinil y el respaldo se cortan con la forma idéntica de tu diseño. El formato rey para regalar o vender suelto.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-200/80">
                            <div class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                02
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-sm text-slate-900">Kiss Cut (Corte superficial de despegue fácil)</h3>
                                <p class="text-xs text-slate-600 mt-0.5">Corte en el vinil con un marco protector cuadrado. Perfecto para eventos masivos donde necesitas despegarlo en 1 segundo.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-200/80">
                            <div class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                03
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-sm text-slate-900">Planillas / Sticker Sheets</h3>
                                <p class="text-xs text-slate-600 mt-0.5">Varios stickers individuales dentro de una misma hoja A5 o A4. La opción favorita de ilustradores para colecciones temáticas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 grid grid-cols-2 gap-4">
                    <div class="bg-gradient-to-br from-orange-100/70 to-orange-50 p-6 rounded-3xl border border-orange-200/80 flex flex-col justify-between h-56 text-center items-center">
                        <span class="text-xs font-bold text-orange-800 uppercase tracking-wide">Forma Die-Cut</span>
                        <div class="w-24 h-24 rounded-full border-2 border-dashed border-orange-500 flex items-center justify-center bg-white shadow-md transform rotate-6">
                            <span class="font-display font-black text-orange-600 text-xs">SILUETA</span>
                        </div>
                        <span class="text-[11px] text-slate-500">Corte 100% contorneado</span>
                    </div>

                    <div class="bg-slate-900 text-white p-6 rounded-3xl border border-slate-800 flex flex-col justify-between h-56 text-center items-center">
                        <span class="text-xs font-bold text-orange-400 uppercase tracking-wide">Kiss-Cut Peel</span>
                        <div class="w-28 h-24 rounded-xl border border-slate-700 bg-slate-800 p-2 flex flex-col items-center justify-center relative">
                            <div class="w-16 h-16 rounded-lg bg-orange-600 text-[10px] font-bold flex items-center justify-center shadow-md">
                                LOGO
                            </div>
                            <span class="absolute -bottom-2 text-[9px] bg-slate-950 px-2 py-0.5 rounded text-orange-300">Marco de apoyo</span>
                        </div>
                        <span class="text-[11px] text-slate-400">Despegue ultra rápido</span>
                    </div>

                    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-56 text-center items-center">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Planillas A5 / A4</span>
                        <div class="w-28 h-24 rounded-lg border border-slate-300 bg-slate-50 p-1.5 grid grid-cols-2 gap-1">
                            <div class="rounded bg-orange-200"></div>
                            <div class="rounded bg-amber-200"></div>
                            <div class="rounded bg-orange-300"></div>
                            <div class="rounded bg-amber-300"></div>
                        </div>
                        <span class="text-[11px] text-slate-500">Múltiples stickers por hoja</span>
                    </div>

                    <div class="bg-gradient-to-tr from-amber-500 to-orange-600 text-white p-6 rounded-3xl shadow-lg flex flex-col justify-between h-56 text-center items-center">
                        <span class="text-xs font-bold text-orange-100 uppercase tracking-wide">Etiquetas de Rollo</span>
                        <div class="w-20 h-20 rounded-full border-4 border-white/40 flex items-center justify-center">
                            <span class="font-display font-black text-sm">PACKAGING</span>
                        </div>
                        <span class="text-[11px] text-orange-100">Para sellado de cajas y bolsas</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED CATALOG DESIGNS SHOWCASE (Real items from database) -->
    <section class="py-20 lg:py-28 bg-[#fafaf9] border-t border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Sticker Cards Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($featuredDesigns as $design)
                    <div class="group bg-white rounded-3xl p-4 border border-slate-200/80 hover:border-orange-400 hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                        <!-- Image Container with Finish Background -->
                        <div class="relative aspect-square rounded-2xl bg-slate-50 overflow-hidden flex items-center justify-center p-4 border border-slate-100 mb-3 group-hover:bg-orange-50/40 transition-colors">
                            @if($design->preview_image_url)
                                <img src="{{ asset('storage/' . $design->preview_image_url) }}"
                                     alt="{{ $design->name }}"
                                     loading="lazy"
                                     class="max-h-full max-w-full object-contain group-hover:scale-108 transition-transform duration-300 drop-shadow-md">
                            @else
                                <div class="w-20 h-20 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-display font-bold text-lg">
                                    {{ Str::limit($design->name, 2, '') }}
                                </div>
                            @endif

                            <!-- SKU Tag -->
                            <span class="absolute top-2.5 left-2.5 text-[10px] font-mono font-bold bg-white/90 text-slate-700 px-2 py-0.5 rounded-md border border-slate-200 shadow-2xs backdrop-blur-xs">
                                {{ $design->sku }}
                            </span>
                        </div>

                        <!-- Card Info -->
                        <div>
                            <h3 class="font-display font-bold text-sm text-slate-900 group-hover:text-orange-600 transition-colors line-clamp-1">
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
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
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
            <div class="mt-12 p-8 rounded-3xl bg-gradient-to-r from-orange-600 to-amber-600 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl">
                <div>
                    <h3 class="font-display font-extrabold text-2xl">¿Tienes tu propio logotipo o diseño?</h3>
                    <p class="text-orange-100 text-sm mt-1 max-w-xl">
                        Fabricamos tus stickers personalizados con corte a la silueta y enviamos la muestra digital de corte gratis antes de imprimir.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <button @click="quoteModal = true"
                            class="px-6 py-3.5 bg-slate-950 hover:bg-black text-white text-xs font-bold rounded-xl shadow-md transition-all active:scale-95">
                        Subir mi propio logo
                    </button>
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
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
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
                    <span class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-display font-extrabold text-base shadow-sm">
                        01
                    </span>
                    <h3 class="font-display font-bold text-base text-slate-900">Elige o Sube tu Arte</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Selecciona un diseño de nuestro catálogo mayorista o envía tu propio archivo vectorial (AI, PDF) o imagen en alta resolución (PNG).
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="relative bg-orange-50/50 p-6 rounded-3xl border border-orange-100 space-y-3">
                    <span class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-display font-extrabold text-base shadow-sm">
                        02
                    </span>
                    <h3 class="font-display font-bold text-base text-slate-900">Muestra Digital Gratis</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Nuestro equipo de pre-prensa traza la línea de corte exacta y te envía una simulación visual para tu aprobación antes de entrar a máquina.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="relative bg-orange-50/50 p-6 rounded-3xl border border-orange-100 space-y-3">
                    <span class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-display font-extrabold text-base shadow-sm">
                        03
                    </span>
                    <h3 class="font-display font-bold text-base text-slate-900">Impresión & Curado UV</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Impresión a 1440 DPI, laminado de protección térmica y troquelado de alta velocidad en un plazo de 48 a 72 hrs hábiles.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="relative bg-orange-50/50 p-6 rounded-3xl border border-orange-100 space-y-3">
                    <span class="w-10 h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center font-display font-extrabold text-base shadow-sm">
                        04
                    </span>
                    <h3 class="font-display font-bold text-base text-slate-900">Empaque & Envío Express</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Agrupados en paquetes de 50 o 100 piezas, protegidos en caja rígida y enviados con número de rastreo inmediato directo a tu puerta.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- WHOLESALE FAQ (Accordion with Alpine.js) -->
    <section id="faq" class="py-20 lg:py-24 bg-[#fafaf9] border-t border-slate-200/60" x-data="{ activeFaq: 1 }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-100 text-orange-800 border border-orange-200">
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
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200" :class="activeFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeFaq === 1" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Nuestro escalafón de mayoreo inicia formalmente desde <strong>50 piezas</strong> por diseño. A partir de 100, 250, 500 y 1,000 piezas se aplican descuentos progresivos automáticos de hasta un 68% de ahorro en comparación con el precio unitario regular.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 2 ? null : 2)"
                            class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Puedo combinar varios diseños en un mismo pedido de mayoreo?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200" :class="activeFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeFaq === 2" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        ¡Sí! Puedes dividir tu pedido en múltiplos de 50 piezas por diseño (por ejemplo, 250 piezas divididas en 5 diseños diferentes de 50 piezas cada uno). Así puedes surtir varios modelos de tu marca al mismo tiempo.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 3 ? null : 3)"
                            class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Qué tan resistentes son al agua, intemperie y lavavajillas?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200" :class="activeFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeFaq === 3" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Son 100% resistentes. Están fabricados sobre vinil automotriz de 4 milésimas con laminado de curado UV. Soportan lavavajillas en ciclo caliente, lluvia torrencial, lavado a presión en autos y exposición solar directa por más de 3 años sin decolorarse.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 4 ? null : 4)"
                            class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Qué formato de archivo debo proporcionar para mis stickers?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200" :class="activeFaq === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeFaq === 4" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Aceptamos archivos vectoriales en <strong>AI, PDF, SVG o EPS</strong> con textos convertidos a curvas, o imágenes en <strong>PNG a 300 DPI con fondo transparente</strong>. Si tu archivo necesita ajustes o trazado de línea de corte, nuestro equipo de diseño te apoya sin costo adicional.
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
                    <button @click="activeFaq = (activeFaq === 5 ? null : 5)"
                            class="w-full text-left p-5 flex items-center justify-between font-display font-bold text-sm text-slate-900 hover:text-orange-600 transition-colors">
                        <span>¿Emiten factura fiscal para empresas y negocios?</span>
                        <svg class="w-5 h-5 text-orange-600 transform transition-transform duration-200" :class="activeFaq === 5 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="activeFaq === 5" x-cloak class="px-5 pb-5 text-xs text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Sí, todos nuestros precios pueden facturarse con CFDI 4.0 oficial (+ IVA). Al concretar tu pedido solo proporciónanos tu Constancia de Situación Fiscal y Uso de CFDI.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FINAL CALL TO ACTION -->
    <section class="py-20 bg-slate-950 text-white relative overflow-hidden">
        <!-- Orange atmospheric glow -->
        <div class="absolute inset-0 bg-gradient-to-r from-orange-950/80 via-slate-950 to-amber-950/70 pointer-events-none"></div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-600/20 text-orange-400 border border-orange-500/30">
                Lleva tu marca al siguiente nivel
            </span>
            <h2 class="font-display font-extrabold text-3xl sm:text-5xl text-white tracking-tight">
                Haz que tus clientes lleven tu marca a todos lados.
            </h2>
            <p class="text-slate-300 max-w-2xl mx-auto text-base leading-relaxed">
                Empieza hoy mismo tu tiraje al mayoreo desde 50 piezas. Recibe tu muestra digital gratuita en menos de 2 horas y asegura la mejor calidad de impresión.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="{{ route('catalogs') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 shadow-xl shadow-orange-600/30 active:scale-95 transition-all">
                    <span>Explorar Catálogo Mayorista</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <button @click="quoteModal = true"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl text-sm font-bold text-slate-200 bg-slate-900 hover:bg-slate-800 border border-slate-700 transition-all">
                    <span>Cotizar Diseño a Medida</span>
                </button>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-14 text-xs border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 text-white font-display font-bold text-lg">
                        <div class="w-7 h-7 rounded-lg bg-orange-600 flex items-center justify-center text-white text-xs">
                            LS
                        </div>
                        <span>LUNA<span class="text-orange-500">STICKERS</span></span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Imprenta especializada en stickers de vinil de alto rendimiento y mayoreo directo a fabricantes, marcas y revendedores en todo México.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm mb-3">Navegación</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="hover:text-orange-400 transition-colors">Inicio</a></li>
                        <li><a href="{{ route('catalogs') }}" class="hover:text-orange-400 transition-colors">Catálogo de Diseños</a></li>
                        <li><a href="#calculadora" class="hover:text-orange-400 transition-colors">Calculadora de Mayoreo</a></li>
                        <li><a href="#acabados" class="hover:text-orange-400 transition-colors">Muestras de Acabados</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm mb-3">Garantía & Envíos</h4>
                    <ul class="space-y-2">
                        <li>• Vinil 100% a prueba de agua</li>
                        <li>• Garantía contra decoloración UV</li>
                        <li>• Muestra previa de corte incluida</li>
                        <li>• Envíos asegurados por DHL / FedEx</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm mb-3">Atención & Pedidos</h4>
                    <p class="text-xs text-slate-400 leading-relaxed mb-3">
                        Lunes a Viernes de 9:00 a 18:00 hrs.<br>
                        Soporte técnico y revisión de archivos vía WhatsApp y correo.
                    </p>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-800 text-orange-400 font-semibold text-xs border border-slate-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Taller Operando en Turno Normal</span>
                    </div>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
                <p>&copy; {{ date('Y') }} Luna Stickers. Todos los derechos reservados.</p>
                <div class="flex gap-4">
                    <span>Términos del Servicio</span>
                    <span>Aviso de Privacidad</span>
                    <span>Políticas de Garantía</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- INTERACTIVE QUICK QUOTE MODAL (Alpine.js) -->
    <div x-show="quoteModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true"
         @keydown.escape.window="quoteModal = false">
        
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <!-- Backdrop -->
            <div x-show="quoteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="quoteModal = false"
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs transition-opacity"></div>

            <!-- Modal Panel -->
            <div x-show="quoteModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                 class="relative inline-block w-full max-w-lg p-6 sm:p-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-orange-200 my-8">
                
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-600 text-white flex items-center justify-center font-bold text-xs">
                            LS
                        </div>
                        <div>
                            <h3 class="font-display font-bold text-base text-slate-900">Cotización Rápida de Mayoreo</h3>
                            <p class="text-[11px] text-slate-500">Muestra digital previa en menos de 2 horas</p>
                        </div>
                    </div>
                    <button @click="quoteModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form class="space-y-4 mt-4" onsubmit="event.preventDefault();">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tu Nombre o Nombre de tu Marca *</label>
                        <input type="text" id="modalName" placeholder="Ej. Carlos / Streetwear Studio" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/50">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Cantidad Estimada *</label>
                            <select id="modalQty" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/50">
                                <option value="50">50 piezas</option>
                                <option value="100">100 piezas</option>
                                <option value="250">250 piezas</option>
                                <option value="500" selected>500 piezas (Recomendado)</option>
                                <option value="1000">1,000 piezas</option>
                                <option value="2500">2,500+ piezas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Acabado Deseado *</label>
                            <select id="modalFinish" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/50">
                                <option value="Tornasol">Tornasol Prisma ✨</option>
                                <option value="Brillante">Brillante High-Gloss</option>
                                <option value="Mate">Mate Aterciopelado</option>
                                <option value="Metálico">Metálico Plata/Oro</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Medida aproximada o detalles</label>
                        <input type="text" id="modalDetails" placeholder="Ej. 7x7 cm, corte die-cut a la silueta" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-hidden focus:ring-2 focus:ring-orange-500 bg-slate-50/50">
                    </div>

                    <div class="pt-2">
                        <button type="button"
                                @click="
                                    let name = document.getElementById('modalName').value || 'Cliente';
                                    let qty = document.getElementById('modalQty').value;
                                    let finish = document.getElementById('modalFinish').value;
                                    let details = document.getElementById('modalDetails').value || 'estándar';
                                    let msg = 'Hola! Mi nombre es ' + name + ' y me interesa cotizar un tiraje al mayoreo de ' + qty + ' stickers en acabado ' + finish + ' (' + details + '). ¿Podrían darme costos y detalles de envío?';
                                    window.open('https://wa.me/5215500000000?text=' + encodeURIComponent(msg), '_blank');
                                    quoteModal = false;
                                "
                                class="w-full py-3.5 px-4 text-xs font-bold text-white bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 rounded-xl shadow-md transition-all active:scale-98 flex items-center justify-center gap-2">
                            <span>Enviar Solicitud por WhatsApp</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
