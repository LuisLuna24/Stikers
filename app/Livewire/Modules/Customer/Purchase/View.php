<?php

namespace App\Livewire\Modules\Customer\Purchase;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class View extends Component
{
    public Order $order;

    public function mount(int $orderId): void
    {
        $customerId = Auth::user()?->customerProfile?->id;

        abort_unless($customerId, 404);

        $this->order = Order::query()
            ->where('customer_id', $customerId)
            ->with([
                'items',
                'payments' => fn($query) => $query->latest(),
            ])
            ->findOrFail($orderId);
    }

    public function statusLabel(?string $status): string
    {
        if (! $status) {
            return 'Sin estado';
        }

        return OrderStatus::tryFromValue($status)?->label() ?? str_replace('_', ' ', ucfirst($status));
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

    /** @return array<int, OrderStatus> */
    public function statusFlow(): array
    {
        return [
            OrderStatus::PendingDeposit,
            OrderStatus::InReview,
            OrderStatus::InProduction,
            OrderStatus::ReadyForPickup,
            OrderStatus::DeliveredBalanceDue,
            OrderStatus::Completed,
        ];
    }

    public function paymentMethodLabel(Payment $payment): string
    {
        return match ($payment->method) {
            'bank_transfer' => 'Transferencia bancaria',
            'cash' => 'Efectivo',
            'other' => $payment->other_method_name ?: 'Otro método',
            default => ucfirst($payment->method),
        };
    }

    public function paymentStatusLabel(string $status): string
    {
        return [
            'recorded' => 'Verificado',
            'pending_verification' => 'Pendiente de verificación',
            'rejected' => 'Rechazado',
            'refunded' => 'Reembolsado',
        ][$status] ?? ucfirst($status);
    }

    public function paymentStatusClasses(string $status): string
    {
        return match ($status) {
            'recorded' => 'text-emerald-700 dark:text-emerald-300',
            'pending_verification' => 'text-amber-700 dark:text-amber-300',
            'rejected' => 'text-rose-700 dark:text-rose-300',
            default => 'text-zinc-600 dark:text-zinc-300',
        };
    }

    public function paymentProofUrl(?string $proofUrl): ?string
    {
        if (blank($proofUrl)) {
            return null;
        }

        return Str::startsWith($proofUrl, ['http://', 'https://'])
            ? $proofUrl
            : Storage::disk('public')->url($proofUrl);
    }

    public function render()
    {
        return view('livewire.modules.customer.purchase.view');
    }
}
