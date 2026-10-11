<?php

namespace App\Livewire\Modules\Admin\Stikers;

use App\Models\Design;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        $design = Design::with('variants')->findOrFail($id);

        if (! $design->is_active) {
            $publishable = filled($design->name)
                && filled($design->sku)
                && filled($design->slug)
                && filled($design->preview_image_url)
                && filled($design->vector_file_url)
                && $design->variants->contains(fn($variant): bool => $variant->is_active
                    && (float) $variant->unit_price > 0
                    && $variant->minimum_quantity === 5
                    && $variant->quantity_increment === 5);

            if (! $publishable) {
                Flux::toast(variant: 'danger', heading: 'No se puede publicar', text: 'Completa imagen, archivo vectorial y al menos una combinación válida antes de publicar.');

                return;
            }

            $design->update(['is_active' => true]);
            Flux::toast(variant: 'success', heading: 'Diseño publicado', text: 'El diseño ya está visible en el catálogo.');

            return;
        }

        $design->update(['is_active' => false]);
        Flux::toast(variant: 'success', heading: 'Diseño desactivado', text: 'El diseño dejó de mostrarse en el catálogo.');
    }

    public function render()
    {
        $designs = Design::query()
            ->withCount(['variants as active_variants_count' => fn($query) => $query->where('is_active', true)])
            ->when($this->search !== '', fn($query) => $query->where(function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            }))
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.modules.admin.stikers.index', compact('designs'));
    }
}
