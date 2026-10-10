<?php

namespace App\Livewire\Modules\Catalogs;

use App\Models\StickerSize;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Flux\Flux;

class Sizes extends Component
{
    use WireUiActions, WithPagination;

    public ?int $sizeId = null;

    public string $name = '';

    public string $width_cm = '';

    public string $height_cm = '';

    public bool $is_active = true;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->modal()->open('cardModal');
    }

    public function edit(int $id): void
    {
        $size = StickerSize::findOrFail($id);

        $this->sizeId = $size->id;
        $this->name = $size->name;
        $this->width_cm = (string) $size->width_cm;
        $this->height_cm = (string) $size->height_cm;
        $this->is_active = $size->is_active;

        $this->resetValidation();
        $this->modal()->open('cardModal');
    }

    public function toggleStatus(int $id): void
    {
        $size = StickerSize::findOrFail($id);
        $size->update(['is_active' => ! $size->is_active]);
        $status = $size->is_active ? 'activo' : 'inactivo';

        Flux::toast(variant: 'success',heading: 'Estatus actualizado',  text: 'El tamaño ahora está '. $status . '.');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('sticker_sizes', 'name')->ignore($this->sizeId),
            ],
            'width_cm' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
            'height_cm' => ['required', 'numeric', 'gt:0', 'max:999999.99'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un tamaño con ese nombre.',
            'width_cm.required' => 'El ancho es obligatorio.',
            'width_cm.numeric' => 'El ancho debe ser numérico.',
            'width_cm.gt' => 'El ancho debe ser mayor que cero.',
            'height_cm.required' => 'El alto es obligatorio.',
            'height_cm.numeric' => 'El alto debe ser numérico.',
            'height_cm.gt' => 'El alto debe ser mayor que cero.',
        ]);

        $size = $this->sizeId
            ? StickerSize::findOrFail($this->sizeId)
            : new StickerSize;

        $size->fill($validated);
        $size->save();

        $this->modal()->close('cardModal');
        $this->resetForm();
        $this->resetPage();

        Flux::toast(variant: 'success',heading: 'Tamaño guardado',  text: 'El tamaño se guardó correctamente.');
    }

    private function resetForm(): void
    {
        $this->reset(['sizeId', 'name', 'width_cm', 'height_cm']);
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $sizes = StickerSize::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('width_cm', 'like', "%{$this->search}%")
                        ->orWhere('height_cm', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.modules.catalogs.sizes', compact('sizes'));
    }
}
