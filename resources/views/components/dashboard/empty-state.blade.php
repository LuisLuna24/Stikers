@props([
    'title' => 'No hay información todavía',
    'description' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->class('flex flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-zinc-200 px-6 py-10 text-center dark:border-zinc-700') }}>
    <span class="flex size-11 items-center justify-center rounded-full bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
        <flux:icon :name="$icon" class="size-5" aria-hidden="true" />
    </span>
    <p class="mt-2 text-sm font-semibold text-zinc-900 dark:text-white">{{ $title }}</p>
    @if ($description)
        <p class="max-w-sm text-sm text-zinc-500 dark:text-zinc-400">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>
