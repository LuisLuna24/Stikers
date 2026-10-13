<?php

namespace App\Livewire\Modules\Admin;

use App\Enums\OrderStatus;
use App\Models\Design;
use App\Models\DesignRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    /** @return array<string, string> */
    public function statusLabels(): array
    {
        return collect(OrderStatus::cases())
            ->mapWithKeys(fn (OrderStatus $status): array => [$status->value => $status->label()])
            ->all();
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

    public function statusLabel(?string $status): string
    {
        return OrderStatus::tryFromValue($status)?->label()
            ?? str_replace('_', ' ', ucfirst($status ?? 'Sin estado'));
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
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $activeStatuses = [
            OrderStatus::PendingDeposit->value,
            OrderStatus::InReview->value,
            OrderStatus::InProduction->value,
            OrderStatus::ReadyForPickup->value,
            OrderStatus::DeliveredBalanceDue->value,
        ];

        $stats = [
            'orders' => Order::query()->count(),
            'active_orders' => Order::query()->whereIn('status', $activeStatuses)->count(),
            'pending_requests' => DesignRequest::query()->whereIn('status', ['submitted', 'reviewing'])->count(),
            'revenue' => Payment::query()
                ->where('status', 'recorded')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('amount'),
            'designs' => Design::query()->where('is_active', true)->count(),
        ];

        $pipeline = collect([
            OrderStatus::PendingDeposit,
            OrderStatus::InReview,
            OrderStatus::InProduction,
            OrderStatus::ReadyForPickup,
            OrderStatus::Completed,
        ])->map(fn (OrderStatus $status): array => [
            'value' => $status->value,
            'label' => $status->label(),
            'count' => Order::query()->where('status', $status->value)->count(),
        ]);

        $recentOrders = Order::query()
            ->with('customer.user')
            ->latest('requested_at')
            ->limit(6)
            ->get();

        $recentRequests = DesignRequest::query()
            ->with(['customer.user', 'desiredSize', 'desiredFinish'])
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.modules.admin.dashboard', compact('stats', 'pipeline', 'recentOrders', 'recentRequests'));
    }
}
