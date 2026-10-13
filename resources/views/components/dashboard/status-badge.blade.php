@props([
    'label',
    'classes' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200',
    'dot' => 'bg-zinc-500',
])

<span {{ $attributes->class("inline-flex w-fit items-center gap-2 rounded-full px-2.5 py-1 text-xs font-medium {$classes}") }}>
    <span class="size-1.5 rounded-full {{ $dot }}" aria-hidden="true"></span>
    {{ $label }}
</span>
