<?php

namespace App\Livewire\Modules\Customer\Purchase;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public ?string $status = null;

    public ?int $selectedOrderId = null;

    public string $paymentAmount = '';

    public string $paymentReference = '';

    public $paymentProof;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function viewOrder(int $orderId): void
    {
        if (! $this->ownedOrderQuery()->whereKey($orderId)->exists()) {
            Flux::toast(variant: 'warning', heading: 'Pedido no disponible', text: 'No encontramos ese pedido en tu cuenta.');

            return;
        }

        $this->selectedOrderId = $orderId;
        $this->resetPaymentForm();
        $this->resetValidation();

        Flux::modal('customer-order-details')->show();
    }

    public function closeOrder(): void
    {
        $this->selectedOrderId = null;
        $this->resetPaymentForm();
        $this->resetValidation();

        Flux::modal('customer-order-details')->close();
    }

    public function submitPaymentProof(): void
    {
        $order = $this->selectedOrder();

        if (! $order) {
            $this->addError('paymentAmount', 'Selecciona un pedido válido.');

            return;
        }

        $availableBalance = $this->availablePaymentBalance($order);

        if ($availableBalance <= 0) {
            $this->addError('paymentAmount', 'Este pedido no tiene saldo disponible para registrar otro pago.');

            return;
        }

        $validated = $this->validate([
            'paymentAmount' => ['required', 'numeric', 'min:0.01', 'max:'.$availableBalance],
            'paymentReference' => ['nullable', 'string', 'max:120'],
            'paymentProof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'paymentAmount.required' => 'Indica el monto de la transferencia.',
            'paymentAmount.max' => 'El comprobante no puede superar el saldo disponible de $'.number_format($availableBalance, 2).'.',
            'paymentProof.required' => 'Adjunta el comprobante de la transferencia.',
            'paymentProof.mimes' => 'El comprobante debe ser una imagen JPG, PNG o un PDF.',
            'paymentProof.max' => 'El comprobante no puede pesar más de 5 MB.',
        ]);

        $proofPath = $this->paymentProof->store('payment-proofs', 'public');

        Payment::create([
            'order_id' => $order->id,
            'method' => 'bank_transfer',
            'status' => 'pending_verification',
            'amount' => $validated['paymentAmount'],
            'paid_at' => now(),
            'reference' => filled($validated['paymentReference']) ? $validated['paymentReference'] : null,
            'proof_url' => $proofPath,
            'note' => 'Comprobante enviado por el cliente.',
        ]);

        $this->resetPaymentForm();
        $this->resetValidation();

        Flux::toast(variant: 'success', heading: 'Comprobante enviado', text: 'El equipo revisará tu transferencia antes de aplicarla al pedido.');
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

    public function proofUrl(?string $proofUrl): ?string
    {
        if (blank($proofUrl)) {
            return null;
        }

        return Str::startsWith($proofUrl, ['http://', 'https://'])
            ? $proofUrl
            : Storage::disk('public')->url($proofUrl);
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

    private function selectedOrder(): ?Order
    {
        if (! $this->selectedOrderId) {
            return null;
        }

        return $this->ownedOrderQuery()
            ->with([
                'items',
                'payments' => fn ($query) => $query->latest(),
            ])
            ->find($this->selectedOrderId);
    }

    private function ownedOrderQuery()
    {
        $customerId = Auth::user()?->customerProfile?->id;

        return Order::query()->when(
            $customerId,
            fn ($query) => $query->where('customer_id', $customerId),
            fn ($query) => $query->whereKey(0),
        );
    }

    public function availablePaymentBalance(Order $order): float
    {
        $pendingVerification = (float) $order->payments
            ->where('status', 'pending_verification')
            ->sum('amount');

        return max(0, (float) $order->total_amount - (float) $order->amount_paid - $pendingVerification);
    }

    private function resetPaymentForm(): void
    {
        $this->reset(['paymentAmount', 'paymentReference', 'paymentProof']);
    }

    public function render()
    {
        $orders = $this->ownedOrderQuery()
            ->withCount('items')
            ->when($this->search !== '', function ($query): void {
                $query->where('order_number', 'like', '%'.$this->search.'%');
            })
            ->when(filled($this->status), fn ($query) => $query->where('status', $this->status))
            ->latest('requested_at')
            ->paginate(10);

        $statusOptions = collect($this->statusFlow())
            ->map(fn (OrderStatus $status): array => ['value' => $status->value, 'label' => $status->label()])
            ->all();

        $summaryQuery = $this->ownedOrderQuery();
        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'active' => (clone $summaryQuery)->whereNotIn('status', [OrderStatus::Completed->value, OrderStatus::Cancelled->value, OrderStatus::Refunded->value])->count(),
            'pending' => (clone $summaryQuery)->where('status', OrderStatus::PendingDeposit->value)->count(),
        ];

        return view('livewire.modules.customer.purchase.index', [
            'orders' => $orders,
            'statusOptions' => $statusOptions,
            'summary' => $summary,
            'selectedOrder' => $this->selectedOrder(),
        ]);
    }
}
