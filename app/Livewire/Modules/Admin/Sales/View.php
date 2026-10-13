<?php

namespace App\Livewire\Modules\Admin\Sales;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class View extends Component
{
    public Order $order;

    public string $paymentMethod = 'bank_transfer';

    public string $paymentAmount = '';

    public string $paymentReference = '';

    public string $otherMethodName = '';

    public string $paymentNote = '';

    public function mount(int $id): void
    {
        $this->loadOrder($id);
    }

    public function addPayment(): void
    {
        $balanceDue = max(0, (float) $this->order->balance_due);

        if ($balanceDue <= 0) {
            $this->addError('paymentAmount', 'Esta venta ya está liquidada.');

            return;
        }

        $validated = $this->validate([
            'paymentMethod' => ['required', Rule::in(['bank_transfer', 'cash', 'other'])],
            'paymentAmount' => ['required', 'numeric', 'min:0.01', 'max:' . $balanceDue],
            'paymentReference' => ['nullable', 'string', 'max:120'],
            'otherMethodName' => ['required_if:paymentMethod,other', 'nullable', 'string', 'max:80'],
            'paymentNote' => ['nullable', 'string', 'max:1000'],
        ], [
            'paymentAmount.required' => 'El monto del pago es requerido.',
            'paymentAmount.max' => 'El pago no puede superar el saldo pendiente de $' . number_format($balanceDue, 2) . '.',
            'otherMethodName.required_if' => 'Indica el nombre del método de pago.',
        ], [
            'paymentAmount' => 'monto de pago'
        ]);

        DB::transaction(function () use ($validated): void {
            $amount = (float) $validated['paymentAmount'];
            $amountPaid = (float) $this->order->amount_paid + $amount;

            Payment::create([
                'order_id' => $this->order->id,
                'received_by_user_id' => auth()->id(),
                'method' => $validated['paymentMethod'],
                'other_method_name' => $validated['paymentMethod'] === 'other' ? ($validated['otherMethodName'] ?: null) : null,
                'status' => 'recorded',
                'amount' => $amount,
                'paid_at' => now(),
                'reference' => $validated['paymentReference'] ?: null,
                'note' => $validated['paymentNote'] ?: null,
            ]);

            $this->order->update([
                'amount_paid' => $amountPaid,
                'balance_due' => max(0, (float) $this->order->total_amount - $amountPaid),
            ]);
        });

        $this->reset(['paymentAmount', 'paymentReference', 'otherMethodName', 'paymentNote']);
        $this->paymentMethod = 'bank_transfer';
        Flux::modal('payment-modal')->close();
        $this->loadOrder($this->order->id);

        Flux::toast(variant: 'success', heading: 'Pago registrado', text: 'El saldo de la venta fue actualizado.');
    }

    public $showPaymentModal = false;
    public function openPaymentModal(): void
    {
        $this->resetValidation();
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->resetValidation();
        $this->reset(['paymentAmount', 'paymentReference', 'otherMethodName', 'paymentNote']);
        $this->paymentMethod = 'bank_transfer';
        Flux::modal('payment-modal')->close();
    }

    public function paymentMethodLabel(Payment $payment): string
    {
        return match ($payment->method) {
            'bank_transfer' => 'Transferencia',
            'cash' => 'Efectivo',
            'other' => $payment->other_method_name ?: 'Otro método',
            default => ucfirst($payment->method),
        };
    }

    public function paymentStatusLabel(string $status): string
    {
        return [
            'recorded' => 'Registrado',
            'pending_verification' => 'Pendiente de verificar',
            'rejected' => 'Rechazado',
            'refunded' => 'Reembolsado',
        ][$status] ?? ucfirst($status);
    }

    private function loadOrder(int $id): void
    {
        $this->order = Order::query()
            ->with(['customer.user', 'shippingAddress', 'items', 'payments.receivedBy', 'invoiceRequest.taxProfile'])
            ->findOrFail($id);
    }

    public function statusLabel(): string
    {
        return OrderStatus::tryFromValue($this->order->status)?->label()
            ?? str_replace('_', ' ', ucfirst($this->order->status));
    }

    public function statusClasses(): string
    {
        return OrderStatus::tryFromValue($this->order->status)?->classes()
            ?? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200';
    }

    public function statusDotClasses(): string
    {
        return OrderStatus::tryFromValue($this->order->status)?->dotClasses() ?? 'bg-zinc-500';
    }

    /** @return array<int, array{value: string, label: string, tone: string}> */
    public function statusActions(): array
    {
        $currentStatus = OrderStatus::tryFromValue($this->order->status);

        $nextStatus = $currentStatus?->next();

        return $nextStatus ? [[
            'value' => $nextStatus->value,
            'label' => $currentStatus->actionLabel(),
            'tone' => 'primary',
        ]] : [];
    }

    public function changeStatus(string $status): void
    {
        $nextStatus = OrderStatus::tryFromValue($status);
        $currentStatus = OrderStatus::tryFromValue($this->order->status);

        if (! $nextStatus || ! $currentStatus || $currentStatus->next() !== $nextStatus) {
            Flux::toast(variant: 'warning', heading: 'Cambio no disponible', text: 'Ese estado no corresponde al flujo actual del pedido.');

            return;
        }

        DB::transaction(function () use ($nextStatus): void {
            $this->order->update(['status' => $nextStatus->value]);

        });

        $this->loadOrder($this->order->id);
        Flux::toast(variant: 'success', heading: 'Estado actualizado', text: 'El pedido ahora está "' . $nextStatus->label() . '".');
    }
    public function render()
    {
        return view('livewire.modules.admin.sales.view');
    }
}
