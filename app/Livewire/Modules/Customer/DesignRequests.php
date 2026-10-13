<?php

namespace App\Livewire\Modules\Customer;

use App\Models\DesignRequest;
use App\Models\StickerFinish;
use App\Models\StickerSize;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DesignRequests extends Component
{
    use WithPagination;

    public string $status = '';

    public string $title = '';

    public string $design_reference_url = '';

    public string $description = '';

    public string $requested_quantity = '';

    public ?int $desired_size_id = null;

    public ?int $desired_finish_id = null;

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function openForm(): void
    {
        $this->resetValidation();
        Flux::modal('customer-design-request')->show();
    }

    public function closeForm(): void
    {
        $this->resetForm();
        Flux::modal('customer-design-request')->close();
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:180'],
            'design_reference_url' => ['required', 'url:http,https', 'max:2048'],
            'description' => ['nullable', 'string'],
            'requested_quantity' => ['nullable', 'integer', 'min:5', 'multiple_of:5'],
            'desired_size_id' => ['nullable', 'integer', 'exists:sticker_sizes,id'],
            'desired_finish_id' => ['nullable', 'integer', 'exists:sticker_finishes,id'],
        ], [
            'title.required' => 'El título es obligatorio.',
            'design_reference_url.required' => 'La URL de referencia es obligatoria.',
            'design_reference_url.url' => 'Captura una URL válida.',
            'requested_quantity.min' => 'La cantidad mínima es de 5 stickers.',
            'requested_quantity.multiple_of' => 'La cantidad debe ser múltiplo de 5.',
        ]);

        $customer = Auth::user()?->customerProfile;

        if (! $customer) {
            $this->addError('title', 'Tu cuenta todavía no tiene un perfil de cliente. Contacta al equipo.');

            return;
        }

        DesignRequest::create([
            ...$validated,
            'customer_id' => $customer->id,
            'status' => 'submitted',
        ]);

        $this->resetForm();
        Flux::modal('customer-design-request')->close();
        Flux::toast(variant: 'success', heading: 'Solicitud enviada', text: 'El equipo revisará tu petición y te contactará.');
    }

    public function statusLabel(?string $status): string
    {
        return [
            'submitted' => 'Nueva',
            'reviewing' => 'En revisión',
            'quoted' => 'Cotizada',
            'accepted' => 'Aceptada',
            'declined' => 'Rechazada',
            'converted_to_order' => 'Convertida a pedido',
            'cancelled' => 'Cancelada',
        ][$status] ?? str_replace('_', ' ', ucfirst($status ?? 'Sin estado'));
    }

    public function statusClasses(?string $status): string
    {
        return match ($status) {
            'accepted', 'converted_to_order' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
            'declined', 'cancelled' => 'bg-rose-50 text-rose-700 dark:bg-rose-400/10 dark:text-rose-300',
            'quoted' => 'bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
            default => 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        };
    }

    public function statusDotClasses(?string $status): string
    {
        return match ($status) {
            'accepted', 'converted_to_order' => 'bg-emerald-500',
            'declined', 'cancelled' => 'bg-rose-500',
            'quoted' => 'bg-blue-500',
            default => 'bg-amber-500',
        };
    }

    private function resetForm(): void
    {
        $this->reset(['title', 'design_reference_url', 'description', 'requested_quantity', 'desired_size_id', 'desired_finish_id']);
        $this->resetValidation();
    }

    public function render()
    {
        $customerId = Auth::user()?->customerProfile?->id;

        $requests = DesignRequest::query()
            ->when($customerId, fn ($query) => $query->where('customer_id', $customerId), fn ($query) => $query->whereKey(0))
            ->with(['desiredSize', 'desiredFinish'])
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(8);

        return view('livewire.modules.customer.design-requests', [
            'requests' => $requests,
            'sizes' => StickerSize::query()->where('is_active', true)->orderBy('name')->get(),
            'finishes' => StickerFinish::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
