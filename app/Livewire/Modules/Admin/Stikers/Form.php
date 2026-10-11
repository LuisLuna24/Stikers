<?php

namespace App\Livewire\Modules\Admin\Stikers;

use App\Models\Design;
use App\Models\DesignVariant;
use App\Models\StickerFinish;
use App\Models\StickerSize;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?int $designId = null;

    public string $sku = '';

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public $vector_file = null;

    public ?string $vector_file_url = null;

    public $preview_image = null;

    public string $current_preview_image_url = '';

    public bool $is_active = true;

    /** @var array<int, array<string, mixed>> */
    public array $variants = [];

    /** @var array<int, int|string> */
    public array $selectedSizeIds = [];

    /** @var array<int, string> */
    public array $selectedFinishKeys = [];

    /** @var array<int, array<string, mixed>> */
    public array $newFinishes = [];

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            return;
        }

        $design = Design::with(['variants', 'finishes'])->findOrFail($id);

        $this->designId = $design->id;
        $this->sku = $design->sku;
        $this->name = $design->name;
        $this->slug = $design->slug;
        $this->description = $design->description ?? '';
        $this->vector_file_url = $design->vector_file_url;
        $this->current_preview_image_url = $design->preview_image_url
            ? (Str::startsWith($design->preview_image_url, ['http://', 'https://'])
                ? $design->preview_image_url
                : Storage::disk('public')->url($design->preview_image_url))
            : '';
        $this->is_active = $design->is_active;

        foreach ($design->variants as $variant) {
            $this->variants[] = $this->variantData(
                sizeId: $variant->sticker_size_id,
                finishKey: 'finish-' . $variant->sticker_finish_id,
                price: (string) $variant->unit_price,
                minimum: $variant->minimum_quantity,
                increment: $variant->quantity_increment,
                active: $variant->is_active,
            );
        }

        $this->selectedSizeIds = collect($this->variants)
            ->pluck('sticker_size_id')
            ->filter()
            ->unique()
            ->values()
            ->all();
        $this->selectedFinishKeys = collect($this->variants)
            ->pluck('finish_key')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function updatedName(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function addVariant(): void
    {
        $this->variants[] = $this->variantData();
    }

    public function addFinish(): void
    {
        $this->newFinishes[] = [
            'name' => '',
            'description' => '',
            'image' => null,
        ];
        $this->selectedFinishKeys[] = 'new-' . array_key_last($this->newFinishes);
    }

    public function removeFinish(int $index): void
    {
        $removedKey = 'new-' . $index;
        unset($this->newFinishes[$index]);
        $this->newFinishes = array_values($this->newFinishes);
        $this->selectedFinishKeys = collect($this->selectedFinishKeys)
            ->reject(fn(string $key): bool => $key === $removedKey)
            ->map(fn(string $key): string => str_starts_with($key, 'new-') && (int) str_replace('new-', '', $key) > $index
                ? 'new-' . ((int) str_replace('new-', '', $key) - 1)
                : $key)
            ->values()
            ->all();
        $this->variants = collect($this->variants)
            ->reject(fn(array $variant): bool => ($variant['finish_key'] ?? null) === $removedKey)
            ->values()
            ->all();
    }

    public function generateVariants(): void
    {
        $sizeIds = collect($this->selectedSizeIds)->filter()->map(fn($id): int => (int) $id)->unique()->values();
        $finishKeys = collect($this->selectedFinishKeys)->filter()->values();
        $existing = collect($this->variants)->keyBy(fn(array $variant): string => ($variant['sticker_size_id'] ?? '') . '-' . ($variant['finish_key'] ?? ''));

        $this->variants = $sizeIds
            ->flatMap(fn(int $sizeId) => $finishKeys->map(function (string $finishKey) use ($sizeId, $existing): array {
                $key = $sizeId . '-' . $finishKey;

                return $existing->get($key) ?? $this->variantData(sizeId: $sizeId, finishKey: $finishKey);
            }))
            ->values()
            ->all();
    }

    public function removeVariant(int $index): void
    {
        if (count($this->variants) === 1) {
            $this->variants = [];

            return;
        }

        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    public function saveDraft(): void
    {
        $this->persist(false);
    }

    public function publish(): void
    {
        $this->persist(true);
    }

    public function save(): void
    {
        $this->persist($this->is_active);
    }

    private function persist(bool $publish): void
    {
        $rules = [
            'sku' => ['required', 'string', 'max:60', Rule::unique('designs', 'sku')->ignore($this->designId)],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:200', 'alpha_dash', Rule::unique('designs', 'slug')->ignore($this->designId)],
            'description' => ['nullable', 'string'],
            'vector_file' => ['nullable', 'file', 'mimes:svg,pdf,ai,eps', 'max:20480'],
            'preview_image' => [$publish && ! $this->hasExistingPreview() ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'variants' => ['nullable', 'array'],
            'variants.*.sticker_size_id' => ['nullable', 'integer', 'exists:sticker_sizes,id'],
            'newFinishes' => ['nullable', 'array'],
            'newFinishes.*.name' => ['required', 'string', 'max:100'],
            'newFinishes.*.description' => ['nullable', 'string', 'max:5000'],
            'newFinishes.*.image' => [$publish ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants.*.finish_key' => ['nullable', 'string'],
            'variants.*.unit_price' => ['nullable', 'numeric', 'gt:0', 'max:99999999.99'],
            'variants.*.minimum_quantity' => ['nullable', 'integer', 'in:5'],
            'variants.*.quantity_increment' => ['nullable', 'integer', 'in:5'],
            'variants.*.is_active' => ['boolean'],
        ];

        $validated = $this->validate($rules, [
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique' => 'Ya existe un diseño con ese SKU.',
            'name.required' => 'El nombre es obligatorio.',
            'slug.required' => 'El slug se genera automáticamente a partir del nombre.',
            'slug.unique' => 'Ya existe un diseño con ese slug.',
            'vector_file.file' => 'El archivo vectorial no es válido.',
            'vector_file.mimes' => 'El archivo vectorial debe ser SVG, PDF, AI o EPS.',
            'vector_file.max' => 'El archivo vectorial no puede superar los 20 MB.',
            'preview_image.required' => 'La imagen de vista previa es obligatoria.',
            'preview_image.image' => 'El archivo debe ser una imagen.',
            'preview_image.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'preview_image.max' => 'La imagen no puede superar los 5 MB.',
            'newFinishes.*.name.required' => 'El nombre del acabado es obligatorio.',
            'newFinishes.*.image.required' => 'La imagen de referencia del acabado es obligatoria para publicar.',
            'newFinishes.*.image.image' => 'La referencia del acabado debe ser una imagen.',
            'newFinishes.*.image.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'newFinishes.*.image.max' => 'La imagen no puede superar los 2 MB.',
            'variants.*.minimum_quantity.in' => 'La cantidad mínima debe ser exactamente 5.',
            'variants.*.quantity_increment.in' => 'El incremento debe ser exactamente 5.',
            'variants.*.unit_price.required' => 'Captura el precio de cada variante.',
            'variants.*.unit_price.gt' => 'El precio debe ser mayor que cero.',
            'variants.*.minimum_quantity.min' => 'La cantidad mínima debe ser de al menos 1.',
            'variants.*.quantity_increment.min' => 'El incremento debe ser de al menos 1.',
        ]);

        $variants = collect($validated['variants'] ?? [])
            ->filter(fn(array $variant): bool => filled($variant['sticker_size_id']) || filled($variant['finish_key']) || filled($variant['unit_price']))
            ->values()
            ->all();

        foreach ($variants as $index => $variant) {
            if (blank($variant['sticker_size_id'] ?? null) || blank($variant['finish_key'] ?? null) || blank($variant['unit_price'] ?? null)) {
                throw ValidationException::withMessages(["variants.{$index}" => 'Completa tamaño, acabado y precio, o elimina la fila.']);
            }
        }

        $newFinishNames = collect($validated['newFinishes'] ?? [])->pluck('name')->map(fn(string $name): string => Str::lower(trim($name)));
        if ($newFinishNames->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages(['newFinishes' => 'No puedes repetir acabados dentro del mismo sticker.']);
        }

        $ownedFinishIds = $this->designId
            ? StickerFinish::query()->where('design_id', $this->designId)->pluck('id')->map(fn(int $id): string => 'finish-' . $id)->all()
            : [];
        $newFinishKeys = collect($validated['newFinishes'] ?? [])->keys()->map(fn(int $index): string => 'new-' . $index)->all();
        $allowedFinishKeys = array_merge($ownedFinishIds, $newFinishKeys);

        foreach ($variants as $index => $variant) {
            if (! in_array($variant['finish_key'], $allowedFinishKeys, true)) {
                throw ValidationException::withMessages(["variants.{$index}.finish_key" => 'Selecciona un acabado perteneciente a este sticker.']);
            }
        }

        if ($publish) {
            if ($variants === []) {
                throw ValidationException::withMessages(['variants' => 'Para publicar debes agregar al menos una combinación activa.']);
            }

            if (! collect($variants)->contains(fn(array $variant): bool => (bool) ($variant['is_active'] ?? true))) {
                throw ValidationException::withMessages(['variants' => 'Para publicar debes tener al menos una combinación activa.']);
            }

            foreach ($variants as $index => $variant) {
                foreach (['sticker_size_id' => 'Selecciona un tamaño.', 'finish_key' => 'Selecciona un acabado.', 'unit_price' => 'Captura un precio.'] as $field => $message) {
                    if (blank($variant[$field] ?? null)) {
                        throw ValidationException::withMessages(["variants.{$index}.{$field}" => $message]);
                    }
                }
            }
        }

        if ($variants !== []) {
            $this->validateUniqueVariants($variants);
        }

        DB::transaction(function () use ($validated, $variants, $publish): void {
            $design = $this->designId ? Design::findOrFail($this->designId) : new Design;
            $design->fill(collect($validated)->except(['variants', 'newFinishes', 'preview_image', 'vector_file'])->all());
            $design->is_active = $publish;

            if ($this->preview_image) {
                $design->preview_image_url = $this->preview_image->store('design-previews', 'public');
            }

            if ($this->vector_file) {
                $design->vector_file_url = $this->vector_file->store('design-vectors', 'public');
            }

            $design->save();

            $finishIds = [];
            foreach ($validated['newFinishes'] ?? [] as $index => $finishData) {
                $finish = StickerFinish::create([
                    'design_id' => $design->id,
                    'name' => trim($finishData['name']),
                    'description' => $finishData['description'] ?? null,
                    'is_active' => true,
                ]);

                if (! empty($finishData['image'])) {
                    $finish->addMedia($finishData['image']->getPathname())
                        ->usingFileName("finish-{$finish->id}.{$finishData['image']->getClientOriginalExtension()}")
                        ->toMediaCollection('image');
                }

                $finishIds['new-' . $index] = $finish->id;
            }

            $keptVariantIds = [];
            foreach ($variants as $variant) {
                $variantModel = DesignVariant::updateOrCreate(
                    [
                        'design_id' => $design->id,
                        'sticker_size_id' => $variant['sticker_size_id'],
                        'sticker_finish_id' => $finishIds[$variant['finish_key']] ?? (int) str_replace('finish-', '', $variant['finish_key']),
                    ],
                    [
                        'unit_price' => $variant['unit_price'],
                        'minimum_quantity' => $variant['minimum_quantity'] ?: 5,
                        'quantity_increment' => $variant['quantity_increment'] ?: 5,
                        'is_active' => $publish && (bool) ($variant['is_active'] ?? true),
                    ],
                );
                $keptVariantIds[] = $variantModel->id;
            }

            if ($design->exists) {
                DesignVariant::where('design_id', $design->id)
                    ->when($keptVariantIds !== [], fn($query) => $query->whereNotIn('id', $keptVariantIds))
                    ->update(['is_active' => false]);
            }
        });

        Flux::toast(
            variant: 'success',
            heading: $publish ? 'Stiker publicado' : ($this->designId ? 'Borrador actualizado' : 'Borrador guardado'),
            text: $publish ? 'El diseño ya está visible en el catálogo.' : 'El diseño se guardó como inactivo.',
        );

        $this->redirectRoute('admin.stikers.index', navigate: true);
    }

    private function hasExistingPreview(): bool
    {
        return $this->designId !== null && filled($this->current_preview_image_url);
    }

    private function validateUniqueVariants(array $variants): void
    {
        $combinations = collect($variants)->map(
            fn(array $variant): string => $variant['sticker_size_id'] . '-' . $variant['finish_key'],
        );

        if ($combinations->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages([
                'variants' => 'No puedes repetir la combinación de tamaño y acabado.',
            ]);
        }
    }

    /** @return array<string, mixed> */
    private function variantData(
        ?int $sizeId = null,
        ?string $finishKey = null,
        string $price = '',
        int $minimum = 5,
        int $increment = 5,
        bool $active = true,
    ): array {
        return [
            'sticker_size_id' => $sizeId,
            'finish_key' => $finishKey,
            'unit_price' => $price,
            'minimum_quantity' => $minimum,
            'quantity_increment' => $increment,
            'is_active' => $active,
        ];
    }

    public function render()
    {
        $finishes = $this->designId
            ? StickerFinish::query()
            ->where('design_id', $this->designId)
            ->where('is_active', true)
            ->with('media')
            ->orderBy('name')
            ->get()
            : collect();

        return view('livewire.modules.admin.stikers.form', [
            'sizes' => StickerSize::query()->where('is_active', true)->orderBy('name')->get(),
            'finishes' => $finishes,
        ]);
    }
}
