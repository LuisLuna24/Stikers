<?php

namespace App\Livewire\Modules\Customer;

use App\Models\Design;
use App\Models\DesignRequest;
use App\Models\StickerFinish;
use App\Models\StickerSize;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Catalog extends Component
{
    public bool $showRequestForm = false;

    public string $title = '';

    public string $design_reference_url = '';

    public string $description = '';

    public string $requested_quantity = '';

    public ?int $desired_size_id = null;

    public ?int $desired_finish_id = null;

    public function saveRequest(): void
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

        $customer = Auth::user()->customerProfile;
        if (! $customer) {
            $this->addError('title', 'Tu cuenta todavía no tiene un perfil de cliente. Contacta al equipo.');

            return;
        }

        DesignRequest::create([
            ...$validated,
            'customer_id' => $customer->id,
            'status' => 'submitted',
        ]);

        $this->reset(['title', 'design_reference_url', 'description', 'requested_quantity', 'desired_size_id', 'desired_finish_id']);
        $this->showRequestForm = false;
        Flux::toast(variant: 'success', heading: 'Solicitud enviada', text: 'El equipo revisará tu petición y te contactará.');
    }

    public function render()
    {
        $designs = Design::query()
            ->active()
            ->whereHas('variants', fn ($query) => $query->active()
                ->whereHas('stickerSize', fn ($sizeQuery) => $sizeQuery->where('is_active', true))
                ->whereHas('stickerFinish', fn ($finishQuery) => $finishQuery->where('is_active', true)))
            ->with(['variants' => fn ($query) => $query->active()
                ->whereHas('stickerSize', fn ($sizeQuery) => $sizeQuery->where('is_active', true))
                ->whereHas('stickerFinish', fn ($finishQuery) => $finishQuery->where('is_active', true))
                ->with(['stickerSize', 'stickerFinish'])])
            ->orderBy('name')
            ->get();

        return view('livewire.modules.customer.catalog', [
            'designs' => $designs,
            'sizes' => StickerSize::query()->where('is_active', true)->orderBy('name')->get(),
            'finishes' => StickerFinish::query()->where('is_active', true)->with('media')->orderBy('name')->get(),
        ]);
    }
}
