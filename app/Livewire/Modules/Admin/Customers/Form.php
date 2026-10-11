<?php

namespace App\Livewire\Modules\Admin\Customers;

use App\Models\CustomerAddress;
use App\Models\CustomerProfile;
use App\Models\TaxProfile;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Form extends Component
{
    public ?int $customerId = null;

    public ?int $userId = null;

    public string $customer_type = 'individual';

    public string $contact_name = '';

    public string $phone = '';

    public string $email = '';

    public bool $hasAddress = false;

    public string $address_label = 'Principal';

    public string $recipient_name = '';

    public string $address_phone = '';

    public string $street = '';

    public string $exterior_number = '';

    public string $interior_number = '';

    public string $neighborhood = '';

    public string $city = '';

    public string $municipality = '';

    public string $state = '';

    public string $postal_code = '';

    public string $country = 'México';

    public string $references = '';

    public bool $wantsInvoice = false;

    public string $legal_name = '';

    public string $rfc = '';

    public string $fiscal_regime_code = '';

    public string $fiscal_postal_code = '';

    public string $cfdi_use_code = 'G03';

    public string $fiscal_email = '';

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            return;
        }

        $customer = CustomerProfile::query()
            ->with(['user', 'addresses' => fn($query) => $query->where('is_default', true), 'taxProfiles' => fn($query) => $query->where('is_default', true)])
            ->findOrFail($id);
        $address = $customer->addresses->first();
        $taxProfile = $customer->taxProfiles->first();

        $this->customerId = $customer->id;
        $this->userId = $customer->user_id;
        $this->customer_type = $customer->customer_type;
        $this->contact_name = $customer->contact_name;
        $this->phone = $customer->phone;
        $this->email = $customer->email;

        if ($address) {
            $this->hasAddress = true;
            $this->address_label = $address->label ?? 'Principal';
            $this->recipient_name = $address->recipient_name ?? $customer->contact_name;
            $this->address_phone = $address->phone ?? $customer->phone;
            $this->street = $address->street;
            $this->exterior_number = $address->exterior_number ?? '';
            $this->interior_number = $address->interior_number ?? '';
            $this->neighborhood = $address->neighborhood;
            $this->city = $address->city;
            $this->municipality = $address->municipality ?? '';
            $this->state = $address->state;
            $this->postal_code = $address->postal_code;
            $this->country = $address->country;
            $this->references = $address->references ?? '';
        }

        if ($taxProfile) {
            $this->wantsInvoice = true;
            $this->legal_name = $taxProfile->legal_name;
            $this->rfc = $taxProfile->rfc;
            $this->fiscal_regime_code = $taxProfile->fiscal_regime_code;
            $this->fiscal_postal_code = $taxProfile->fiscal_postal_code;
            $this->cfdi_use_code = $taxProfile->cfdi_use_code;
            $this->fiscal_email = $taxProfile->fiscal_email ?? '';
        }
    }

    public function updatedHasAddress(bool $value): void
    {
        if ($value && blank($this->recipient_name)) {
            $this->recipient_name = $this->contact_name;
            $this->address_phone = $this->phone;
        }
    }

    public function updatedWantsInvoice(bool $value): void
    {
        if ($value && blank($this->legal_name)) {
            $this->legal_name = $this->contact_name;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'customer_type' => ['required', Rule::in(['individual', 'business'])],
            'contact_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($this->userId)],
            'hasAddress' => ['boolean'],
            'address_label' => ['nullable', 'string', 'max:80'],
            'recipient_name' => [Rule::requiredIf($this->hasAddress), 'nullable', 'string', 'max:150'],
            'address_phone' => ['nullable', 'string', 'max:30'],
            'street' => [Rule::requiredIf($this->hasAddress), 'nullable', 'string', 'max:180'],
            'exterior_number' => ['nullable', 'string', 'max:30'],
            'interior_number' => ['nullable', 'string', 'max:30'],
            'neighborhood' => [Rule::requiredIf($this->hasAddress), 'nullable', 'string', 'max:120'],
            'city' => [Rule::requiredIf($this->hasAddress), 'nullable', 'string', 'max:120'],
            'municipality' => ['nullable', 'string', 'max:120'],
            'state' => [Rule::requiredIf($this->hasAddress), 'nullable', 'string', 'max:120'],
            'postal_code' => [Rule::requiredIf($this->hasAddress), 'nullable', 'string', 'max:10'],
            'country' => [Rule::requiredIf($this->hasAddress), 'nullable', 'string', 'max:80'],
            'references' => ['nullable', 'string', 'max:1000'],
            'wantsInvoice' => ['boolean'],
            'legal_name' => [Rule::requiredIf($this->wantsInvoice), 'nullable', 'string', 'max:200'],
            'rfc' => [Rule::requiredIf($this->wantsInvoice), 'nullable', 'string', 'max:13', 'min:12'],
            'fiscal_regime_code' => [Rule::requiredIf($this->wantsInvoice), 'nullable', 'string', 'max:10'],
            'fiscal_postal_code' => [Rule::requiredIf($this->wantsInvoice), 'nullable', 'string', 'max:10'],
            'cfdi_use_code' => [Rule::requiredIf($this->wantsInvoice), 'nullable', 'string', 'max:10'],
            'fiscal_email' => ['nullable', 'email', 'max:190'],
        ], [
            'contact_name.required' => 'Indica el nombre del cliente o negocio.',
            'phone.required' => 'El teléfono es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Ya existe un usuario registrado con este correo.',
            'email.email' => 'Escribe un correo electrónico válido.',
            '*.required' => 'Este campo es obligatorio.',
            'rfc.min' => 'El RFC debe tener entre 12 y 13 caracteres.',
        ]);

        DB::transaction(function () use ($validated): void {
            $user = $this->userId ? User::findOrFail($this->userId) : new User;
            $user->fill([
                'name' => trim($validated['contact_name']),
                'email' => strtolower(trim($validated['email'])),
                'phone' => trim($validated['phone']),
                'status' => 'active',
            ]);
            if (! $user->exists) {
                $user->password = Str::random(40);
                $user->email_verified_at = now();
            }
            $user->save();
            if (! $user->hasRole('cliente')) {
                $user->assignRole('cliente');
            }

            $customer = $this->customerId ? CustomerProfile::findOrFail($this->customerId) : new CustomerProfile;
            $customer->fill([
                'user_id' => $user->id,
                'customer_type' => $validated['customer_type'],
                'contact_name' => trim($validated['contact_name']),
                'phone' => trim($validated['phone']),
                'email' => strtolower(trim($validated['email'])),
            ]);
            $customer->save();

            if ($this->hasAddress) {
                CustomerAddress::updateOrCreate(
                    ['customer_id' => $customer->id, 'is_default' => true],
                    [
                        'label' => trim($validated['address_label'] ?? '') ?: 'Principal',
                        'recipient_name' => trim($validated['recipient_name']),
                        'phone' => trim($validated['address_phone'] ?? '') ?: $validated['phone'],
                        'street' => trim($validated['street']),
                        'exterior_number' => trim($validated['exterior_number'] ?? ''),
                        'interior_number' => trim($validated['interior_number'] ?? ''),
                        'neighborhood' => trim($validated['neighborhood']),
                        'city' => trim($validated['city']),
                        'municipality' => trim($validated['municipality'] ?? ''),
                        'state' => trim($validated['state']),
                        'postal_code' => trim($validated['postal_code']),
                        'country' => trim($validated['country'] ?? 'México'),
                        'references' => trim($validated['references'] ?? ''),
                        'is_default' => true,
                    ],
                );
            } else {
                CustomerAddress::query()
                    ->where('customer_id', $customer->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            if ($this->wantsInvoice) {
                TaxProfile::updateOrCreate(
                    ['customer_id' => $customer->id, 'is_default' => true],
                    [
                        'tax_person_type' => $validated['customer_type'],
                        'legal_name' => trim($validated['legal_name']),
                        'rfc' => strtoupper(trim($validated['rfc'])),
                        'fiscal_regime_code' => trim($validated['fiscal_regime_code']),
                        'fiscal_postal_code' => trim($validated['fiscal_postal_code']),
                        'cfdi_use_code' => strtoupper(trim($validated['cfdi_use_code'])),
                        'fiscal_email' => filled($validated['fiscal_email'] ?? null) ? strtolower(trim($validated['fiscal_email'])) : null,
                        'is_default' => true,
                    ],
                );
            } else {
                TaxProfile::query()
                    ->where('customer_id', $customer->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });

        Flux::toast(
            variant: 'success',
            heading: $this->customerId ? 'Cliente actualizado' : 'Cliente registrado',
            text: $this->customerId ? 'Los datos del cliente se actualizaron correctamente.' : 'El cliente ya está disponible para registrar pedidos.',
        );

        $this->redirectRoute('admin.customers.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.modules.admin.customers.form');
    }
}
