<?php

namespace App\Livewire\Modules\Catalogs;

use App\Models\StickerFinish;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;

class Finishes extends Component
{
    use WireUiActions, WithFileUploads, WithPagination;

    public ?int $finishId = null;

    public string $name = '';

    public string $description = '';

    public $image = null;

    public string $currentImageUrl = '';

    public bool $is_active = true;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->modal()->open('finishModal');
    }

    public function edit(int $id): void
    {
        $finish = StickerFinish::findOrFail($id);

        $this->finishId = $finish->id;
        $this->name = $finish->name;
        $this->description = $finish->description ?? '';
        $this->image = null;
        $this->currentImageUrl = $finish->getFirstMediaUrl('image');
        $this->is_active = $finish->is_active;

        $this->resetValidation();
        $this->modal()->open('finishModal');
    }

    public function toggleStatus(int $id): void
    {
        $finish = StickerFinish::findOrFail($id);
        $finish->update(['is_active' => ! $finish->is_active]);
        $status = $finish->is_active ? 'activo' : 'inactivo';

        $this->notification()->success(
            title: 'Estatus actualizado',
            description: "El acabado ahora está {$status}."
        );
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('sticker_finishes', 'name')->ignore($this->finishId),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'name.unique' => 'Ya existe un acabado con ese nombre.',
            'description.max' => 'La descripción no puede superar los 5000 caracteres.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser JPG, JPEG, PNG o WEBP.',
            'image.max' => 'La imagen no puede superar los 2 MB.',
        ]);

        $finish = $this->finishId
            ? StickerFinish::findOrFail($this->finishId)
            : new StickerFinish;

        $finish->fill($validated);
        $finish->save();

        if ($this->image) {
            $finish->clearMediaCollection('image');
            $finish->addMedia($this->image->getPathname())
                ->usingFileName("finish-{$finish->id}.{$this->image->getClientOriginalExtension()}")
                ->toMediaCollection('image');
        }

        $this->modal()->close('finishModal');
        $this->resetForm();
        $this->resetPage();

        $this->notification()->success(
            title: 'Acabado guardado',
            description: 'El acabado se guardó correctamente.'
        );
    }

    private function resetForm(): void
    {
        $this->reset(['finishId', 'name', 'description', 'image', 'currentImageUrl']);
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $finishes = StickerFinish::query()
            ->with('media')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.modules.catalogs.finishes', compact('finishes'));
    }
}
