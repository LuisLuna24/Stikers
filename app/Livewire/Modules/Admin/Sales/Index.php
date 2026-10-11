<?php

namespace App\Livewire\Modules\Admin\Sales;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function statusLabel(string $status): string
    {
        return [
            'pending_deposit' => 'Anticipo pendiente',
            'in_review' => 'En revisión',
            'pending_customer_changes' => 'Cambios del cliente',
            'awaiting_deposit' => 'Esperando anticipo',
            'in_production' => 'En producción',
            'ready_for_pickup' => 'Lista para entrega',
            'delivered_balance_due' => 'Entregada · saldo pendiente',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'refunded' => 'Reembolsada',
        ][$status] ?? str_replace('_', ' ', ucfirst($status));
    }

    public function statusClasses(string $status): string
    {
        return match ($status) {
            'completed', 'ready_for_pickup' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
            'in_production' => 'bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
            'cancelled', 'refunded' => 'bg-rose-50 text-rose-700 dark:bg-rose-400/10 dark:text-rose-300',
            default => 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        };
    }

    public function render()
    {
        $orders = Order::query()
            ->with(['customer.user', 'invoiceRequest'])
            ->withSum('items', 'quantity')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('order_number', 'like', "%{$this->search}%")
                        ->orWhereHas('customer', function ($customerQuery): void {
                            $customerQuery->where('contact_name', 'like', "%{$this->search}%")
                                ->orWhere('email', 'like', "%{$this->search}%")
                                ->orWhere('phone', 'like', "%{$this->search}%")
                                ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$this->search}%"));
                        });
                });
            })
            ->when($this->status !== '', fn($query) => $query->where('status', $this->status))
            ->latest('requested_at')
            ->paginate(10);

        $statusOptions = [
            'pending_deposit' => 'Anticipo pendiente',
            'in_review' => 'En revisión',
            'pending_customer_changes' => 'Cambios del cliente',
            'awaiting_deposit' => 'Esperando anticipo',
            'in_production' => 'En producción',
            'ready_for_pickup' => 'Lista para entrega',
            'delivered_balance_due' => 'Entregada · saldo pendiente',
            'completed' => 'Completada',
            'cancelled' => 'Cancelada',
            'refunded' => 'Reembolsada',
        ];

        $summary = [
            'total' => Order::count(),
            'production' => Order::where('status', 'in_production')->count(),
            'pending' => Order::whereIn('status', ['pending_deposit', 'awaiting_deposit'])->count(),
        ];

        return view('livewire.modules.admin.sales.index', compact('orders', 'statusOptions', 'summary'));
    }
}
