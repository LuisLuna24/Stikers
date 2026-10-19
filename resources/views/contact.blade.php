<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contacto | Two Brothers Stickers</title>
    <meta name="description"
        content="Cuéntanos sobre tu proyecto de stickers de vinil. Recibe orientación sobre materiales, acabados, cantidades y tiempos de producción.">

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">

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
                                500: '#f97316',
                                600: '#ea580c',
                                700: '#c2410c',
                                950: '#431407'
                            }
                        },
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui'],
                            display: ['"Space Grotesk"', 'sans-serif']
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
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            color: #0f172a;
            background: #fafaf9;
        }

        .font-display {
            font-family: 'Space Grotesk', system-ui, sans-serif;
        }

        .bg-grid-pattern {
            background-size: 32px 32px;
            background-image: linear-gradient(to right, rgba(234, 88, 12, .055) 1px, transparent 1px), linear-gradient(to bottom, rgba(234, 88, 12, .055) 1px, transparent 1px);
        }
    </style>
</head>

<body class="min-h-screen bg-[#fafaf9] text-slate-900 selection:bg-orange-500 selection:text-white"
    x-data="{
        mobileMenu: false,
        quoteModal: false
    }">

    <x-home.navbar />

    <main>
        <section
            class="relative overflow-hidden border-b border-orange-100/80 bg-gradient-to-b from-orange-50/70 via-white to-[#fafaf9]">
            <div class="absolute inset-0 bg-grid-pattern opacity-70 pointer-events-none"></div>
            <div
                class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-orange-200/30 blur-3xl pointer-events-none">
            </div>
            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
                <div class="mx-auto max-w-3xl text-center">
                    <h1
                        class="font-display text-4xl font-extrabold leading-[1.08] tracking-[-.03em] text-slate-950 sm:text-5xl lg:text-6xl">
                        Estamos listos para <span class="text-orange-600">atenderte.</span>
                    </h1>
                    <p class="mx-auto mt-6 max-w-xl text-base leading-relaxed text-slate-600 sm:text-lg">
                        Escríbenos por WhatsApp y te ayudamos con tus stickers de vinil, acabados, cantidades y pedidos.
                    </p>

                    <div class="mx-auto mt-10 grid max-w-2xl gap-5 text-left sm:grid-cols-2">
                        <a href="https://wa.me/5215500000000" target="_blank" rel="noopener"
                            class="group flex items-center gap-4 rounded-2xl border border-emerald-200/80 bg-white p-5 shadow-lg shadow-orange-950/5 transition hover:-translate-y-0.5 hover:border-emerald-300">
                            <span
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200/70">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-brand-whatsapp">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 21l1.65 -3.8a9 9 0 1 1 3.4 2.9l-5.05 .9" />
                                    <path
                                        d="M9 10a.5 .5 0 0 0 1 0v-1a.5 .5 0 0 0 -1 0v1a5 5 0 0 0 5 5h1a.5 .5 0 0 0 0 -1h-1a.5 .5 0 0 0 0 1" />
                                </svg>
                            </span>
                            <span><span
                                    class="block text-xs font-bold uppercase tracking-wider text-slate-400">Respuesta
                                    rápida</span><span
                                    class="font-semibold text-slate-800 group-hover:text-emerald-600">Escríbenos por
                                    WhatsApp</span></span>
                        </a>
                        <div class="flex items-center gap-4 p-2">
                            <span
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600 ring-1 ring-orange-200/70">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M12 3v18m9-9H3" />
                                </svg>
                            </span>
                            <span><span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Horario
                                    de atención</span><span class="font-semibold text-slate-800">Lun–Vie · 9:00–18:00
                                    h</span></span>
                        </div>
                    </div>
                </div>
        </section>
    </main>

    <x-home.footer />
</body>

</html>
