<?php

namespace App\Livewire\Modules\Admin\Customers;

use App\Models\CustomerProfile;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $customerType = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCustomerType(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = CustomerProfile::query()
            ->with(['user', 'addresses' => fn($query) => $query->where('is_default', true)])
            ->withCount('orders')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('contact_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhereHas('user', fn($userQuery) => $userQuery->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->customerType !== '', fn($query) => $query->where('customer_type', $this->customerType))
            ->latest()
            ->paginate(10);

        $summary = [
            'total' => CustomerProfile::count(),
            'individuals' => CustomerProfile::where('customer_type', 'individual')->count(),
            'businesses' => CustomerProfile::where('customer_type', 'business')->count(),
        ];

        $typeOptions = [
            ['name' => 'Todos los tipos', 'id' => ''],
            ['name' => 'Personas', 'id' => 'individual'],
            ['name' => 'Negocios', 'id' => 'business'],
        ];

        return view('livewire.modules.admin.customers.index', compact('customers', 'summary', 'typeOptions'));
    }
}
