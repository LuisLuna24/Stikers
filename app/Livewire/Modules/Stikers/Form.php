<?php

namespace App\Livewire\Modules\Stikers;

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

    public ?string $vector_file_url = null;

    public $preview_image = null;

    public string $current_preview_image_url = '';

    public bool $is_active = true;

    /** @var array<int, array<string, mixed>> */
    public array $variants = [];

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            $this->addVariant();

            return;
        }

        $design = Design::with('variants')->findOrFail($id);

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
                finishId: $variant->sticker_finish_id,
                price: (string) $variant->unit_price,
                minimum: $variant->minimum_quantity,
                increment: $variant->quantity_increment,
                active: $variant->is_active,
            );
        }

        if ($this->variants === []) {
            $this->addVariant();
        }
    }

    public function updatedName(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function addVariant(): void
    {
        $this->variants[] = $this->variantData();
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

    public function save(): void
    {
        $validated = $this->validate([
            'sku' => ['required', 'string', 'max:60', Rule::unique('designs', 'sku')->ignore($this->designId)],
            'name' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:200', 'alpha_dash', Rule::unique('designs', 'slug')->ignore($this->designId)],
            'description' => ['nullable', 'string'],
            'vector_file_url' => ['nullable', 'url:http,https', 'max:2048'],
            'preview_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['boolean'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.sticker_size_id' => ['required', 'integer', 'exists:sticker_sizes,id'],
            'variants.*.sticker_finish_id' => ['required', 'integer', 'exists:sticker_finishes,id'],
            'variants.*.unit_price' => ['required', 'numeric', 'gt:0', 'max:99999999.99'],
            'variants.*.minimum_quantity' => ['required', 'integer', 'min:1'],
            'variants.*.quantity_increment' => ['required', 'integer', 'min:1'],
            'variants.*.is_active' => ['boolean'],
        ], [
            'sku.required' => 'El SKU es obligatorio.',
            'sku.unique' => 'Ya existe un diseño con ese SKU.',
            'name.required' => 'El nombre es obligatorio.',
            'slug.required' => 'El slug se genera automáticamente a partir del nombre.',
            'slug.unique' => 'Ya existe un diseño con ese slug.',
            'vector_file_url.url' => 'La URL del archivo vectorial no es válida.',
            'preview_image.image' => 'El archivo debe ser una imagen.',
            'preview_image.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'preview_image.max' => 'La imagen no puede superar los 5 MB.',
            'variants.required' => 'Agrega al menos una variante.',
            'variants.*.unit_price.required' => 'Captura el precio de cada variante.',
            'variants.*.unit_price.gt' => 'El precio debe ser mayor que cero.',
            'variants.*.minimum_quantity.min' => 'La cantidad mínima debe ser de al menos 1.',
            'variants.*.quantity_increment.min' => 'El incremento debe ser de al menos 1.',
        ]);

        $this->validateUniqueVariants($validated['variants']);

        DB::transaction(function () use ($validated): void {
            $design = $this->designId ? Design::findOrFail($this->designId) : new Design;
            $design->fill(collect($validated)->except(['variants', 'preview_image'])->all());

            if ($this->preview_image) {
                $design->preview_image_url = $this->preview_image->store('design-previews', 'public');
            }

            $design->save();

            foreach ($validated['variants'] as $variant) {
                DesignVariant::updateOrCreate(
                    [
                        'design_id' => $design->id,
                        'sticker_size_id' => $variant['sticker_size_id'],
                        'sticker_finish_id' => $variant['sticker_finish_id'],
                    ],
                    [
                        'unit_price' => $variant['unit_price'],
                        'minimum_quantity' => $variant['minimum_quantity'],
                        'quantity_increment' => $variant['quantity_increment'],
                        'is_active' => $variant['is_active'],
                    ],
                );
            }
        });

        Flux::toast(
            variant: 'success',
            heading: $this->designId ? 'Stiker actualizado' : 'Stiker creado',
            text: 'El diseño y sus variantes se guardaron correctamente.',
        );

        $this->redirectRoute('admin.stikers.index', navigate: true);
    }

    private function validateUniqueVariants(array $variants): void
    {
        $combinations = collect($variants)->map(
            fn (array $variant): string => $variant['sticker_size_id'].'-'.$variant['sticker_finish_id'],
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
        ?int $finishId = null,
        string $price = '',
        int $minimum = 5,
        int $increment = 5,
        bool $active = true,
    ): array {
        return [
            'sticker_size_id' => $sizeId,
            'sticker_finish_id' => $finishId,
            'unit_price' => $price,
            'minimum_quantity' => $minimum,
            'quantity_increment' => $increment,
            'is_active' => $active,
        ];
    }

    public function render()
    {
        return view('livewire.modules.stikers.form', [
            'sizes' => StickerSize::query()->where('is_active', true)->orderBy('name')->get(),
            'finishes' => StickerFinish::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
