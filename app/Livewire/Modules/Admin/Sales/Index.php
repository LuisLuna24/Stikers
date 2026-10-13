<?php

namespace App\Livewire\Modules\Admin\Sales;

use App\Enums\OrderStatus;
use App\Models\Order;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public ?string $status = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $nextStatus = OrderStatus::tryFromValue($status);

        if (! $nextStatus) {
            return;
        }

        $order = Order::findOrFail($orderId);
        $currentStatus = OrderStatus::tryFromValue($order->status);

        if (! $currentStatus || ($nextStatus === OrderStatus::Cancelled
            ? ! $currentStatus->canCancel()
            : $currentStatus->next() !== $nextStatus)) {
            Flux::toast(variant: 'warning', heading: 'Cambio no disponible', text: 'Ese pedido no puede pasar a ese estado desde su estado actual.');

            return;
        }

        DB::transaction(function () use ($order, $nextStatus): void {
            $order->update(['status' => $nextStatus->value]);

        });

        Flux::toast(variant: 'success', heading: 'Estado actualizado', text: 'El estado del pedido fue actualizado.');
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
        return OrderStatus::tryFromValue($status)?->classes() ?? 'bg-zinc-100 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200';
    }

    //!==============================================Acciones

    public $actionsModal = false;
    public $viewId;

    public function openActionsModal($id)
    {
        $this->viewId = $id;
        $this->actionsModal = true;
    }

    /** @return array<int, array{key: string, icon: string, title: string, desc: string, tone: string}> */
    public function actionOptions(): array
    {
        $order = $this->viewId ? Order::find($this->viewId) : null;
        $currentStatus = OrderStatus::tryFromValue($order?->status);
        $nextStatus = $currentStatus?->next();
        $actions = [[
            'key' => 'view',
            'icon' => 'eye',
            'title' => 'Ver pedido',
            'desc' => 'Consulta los detalles sin modificar nada',
            'tone' => 'gray',
        ]];

        if ($nextStatus) {
            $actions[] = [
                'key' => 'next',
                'icon' => 'arrow-right',
                'title' => $currentStatus->actionLabel(),
                'desc' => 'Avanza al siguiente paso del flujo',
                'tone' => 'blue',
            ];
        }

        return $actions;
    }

    public function canCancel(): bool
    {
        $order = $this->viewId ? Order::find($this->viewId) : null;
        $currentStatus = OrderStatus::tryFromValue($order?->status);

        return $currentStatus?->canCancel() ?? false;
    }

    public function selectAction(string $action): void
    {
        $this->actionsModal = false;

        if ($action === 'view') {
            $this->redirectRoute('admin.sales.view', ['id' => $this->viewId]);

            return;
        }

        $order = Order::findOrFail($this->viewId);
        $currentStatus = OrderStatus::tryFromValue($order->status);
        $status = match ($action) {
            'next' => $currentStatus?->next()?->value,
            'cancel' => OrderStatus::Cancelled->value,
            default => null,
        };

        if ($status) {
            $this->updateStatus((int) $this->viewId, $status);
        }
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
            ->when(filled($this->status), fn($query) => $query->where('status', $this->status))
            ->latest('requested_at')
            ->paginate(10);

        $statusOptions = collect(OrderStatus::cases())
            ->map(fn (OrderStatus $status): array => ['value' => $status->value, 'label' => $status->label()])
            ->prepend(['value' => '', 'label' => 'Todos los estados'])
            ->values()
            ->all();

        $summary = [
            'total' => Order::count(),
            'production' => Order::where('status', 'in_production')->count(),
            'pending' => Order::where('status', OrderStatus::PendingDeposit->value)->count(),
        ];

        return view('livewire.modules.admin.sales.index', compact('orders', 'statusOptions', 'summary'));
    }
}
