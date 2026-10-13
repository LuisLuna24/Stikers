<?php

namespace App\Livewire\Modules\Customer;

use App\Enums\OrderStatus;
use App\Models\DesignRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function statusLabel(?string $status): string
    {
        return OrderStatus::tryFromValue($status)?->label()
            ?? str_replace('_', ' ', ucfirst($status ?? 'Sin estado'));
    }

    public function statusClasses(?string $status): string
    {
        return OrderStatus::tryFromValue($status)?->classes()
            ?? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200';
    }

    public function statusDotClasses(?string $status): string
    {
        return OrderStatus::tryFromValue($status)?->dotClasses() ?? 'bg-zinc-500';
    }

    public function requestStatusLabel(?string $status): string
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

    public function requestStatusClasses(?string $status): string
    {
        return match ($status) {
            'accepted', 'converted_to_order' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
            'declined', 'cancelled' => 'bg-rose-50 text-rose-700 dark:bg-rose-400/10 dark:text-rose-300',
            'quoted' => 'bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
            default => 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        };
    }

    public function render()
    {
        $customer = Auth::user()?->customerProfile;
        $ordersQuery = Order::query()->when(
            $customer,
            fn ($query) => $query->where('customer_id', $customer->id),
            fn ($query) => $query->whereKey(0),
        );

        $stats = [
            'orders' => (clone $ordersQuery)->count(),
            'active_orders' => (clone $ordersQuery)->whereNotIn('status', [
                OrderStatus::Completed->value,
                OrderStatus::Cancelled->value,
                OrderStatus::Refunded->value,
            ])->count(),
            'balance' => (clone $ordersQuery)->sum('balance_due'),
            'requests' => DesignRequest::query()->when(
                $customer,
                fn ($query) => $query->where('customer_id', $customer->id),
                fn ($query) => $query->whereKey(0),
            )->whereIn('status', ['submitted', 'reviewing', 'quoted'])->count(),
        ];

        $recentOrders = (clone $ordersQuery)
            ->withCount('items')
            ->latest('requested_at')
            ->limit(4)
            ->get();

        $recentRequests = DesignRequest::query()
            ->when(
                $customer,
                fn ($query) => $query->where('customer_id', $customer->id),
                fn ($query) => $query->whereKey(0),
            )
            ->latest()
            ->limit(3)
            ->get();

        return view('livewire.modules.customer.dashboard', compact('customer', 'stats', 'recentOrders', 'recentRequests'));
    }
}
