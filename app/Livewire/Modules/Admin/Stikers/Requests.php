<?php

namespace App\Livewire\Modules\Admin\Stikers;

use App\Models\DesignRequest;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

class Requests extends Component
{
    use WithPagination;

    public string $status = '';

    /** @var array<string, string> */
    public array $statuses = [
        'submitted' => 'Nueva',
        'reviewing' => 'En revisión',
        'quoted' => 'Cotizada',
        'accepted' => 'Aceptada',
        'declined' => 'Rechazada',
        'converted_to_order' => 'Convertida a pedido',
        'cancelled' => 'Cancelada',
    ];

    public function updateStatus(int $id, string $status): void
    {
        if (! array_key_exists($status, $this->statuses)) {
            return;
        }

        DesignRequest::findOrFail($id)->update(['status' => $status]);
        Flux::toast(variant: 'success', heading: 'Solicitud actualizada', text: 'El estado se guardó correctamente.');
    }

    public function render()
    {
        $requests = DesignRequest::query()
            ->with(['customer.user', 'desiredSize', 'desiredFinish'])
            ->when($this->status !== '', fn($query) => $query->where('status', $this->status))
            ->latest('id')
            ->paginate(10);

        return view('livewire.modules.admin.stikers.requests', compact('requests'));
    }
}
