@props([
    'label',
    'value',
    'detail' => null,
    'icon' => 'chart-bar',
    'tone' => 'indigo',
])

@php
    $toneClasses = [
        'indigo' => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300',
        'emerald' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
        'amber' => 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        'blue' => 'bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
        'rose' => 'bg-rose-50 text-rose-700 dark:bg-rose-400/10 dark:text-rose-300',
    ];
@endphp

<article {{ $attributes->class('rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900') }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
            <p class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 tabular-nums dark:text-white">{{ $value }}</p>
            @if ($detail)
                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $detail }}</p>
            @endif
        </div>
        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl {{ $toneClasses[$tone] ?? $toneClasses['indigo'] }}">
            <flux:icon :name="$icon" class="size-5" aria-hidden="true" />
        </span>
    </div>
</article>
