<?php

namespace App\Livewire\Modules\Admin\Sales;

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
            'paymentAmount.max' => 'El pago no puede superar el saldo pendiente de $' . number_format($balanceDue, 2) . '.',
            'otherMethodName.required_if' => 'Indica el nombre del método de pago.',
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

    public function openPaymentModal(): void
    {
        $this->resetValidation();
        Flux::modal('payment-modal')->show();
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
        ][$this->order->status] ?? str_replace('_', ' ', ucfirst($this->order->status));
    }

    public function statusClasses(): string
    {
        return match ($this->order->status) {
            'completed', 'ready_for_pickup' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300',
            'in_production' => 'bg-blue-50 text-blue-700 dark:bg-blue-400/10 dark:text-blue-300',
            'cancelled', 'refunded' => 'bg-rose-50 text-rose-700 dark:bg-rose-400/10 dark:text-rose-300',
            default => 'bg-amber-50 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300',
        };
    }

    public function statusDotClasses(): string
    {
        return match ($this->order->status) {
            'completed', 'ready_for_pickup' => 'bg-emerald-500',
            'in_production' => 'bg-blue-500',
            'cancelled', 'refunded' => 'bg-rose-500',
            default => 'bg-amber-500',
        };
    }

    public function render()
    {
        return view('livewire.modules.admin.sales.view');
    }
}
