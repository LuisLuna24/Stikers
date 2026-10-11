<?php

namespace Database\Seeders;

use App\Models\CustomerAddress;
use App\Models\CustomerProfile;
use App\Models\DesignVariant;
use App\Models\Order;
use App\Models\OrderInvoiceRequest;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\TaxProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSalesSeeder extends Seeder
{
    public function run(): void
    {
        $customers = collect([
            ['email' => 'ana@demo.test', 'name' => 'Ana López', 'phone' => '5551001001', 'type' => 'individual'],
            ['email' => 'estudio@demo.test', 'name' => 'Estudio Norte', 'phone' => '5551001002', 'type' => 'business', 'rfc' => 'ENO240101AB1'],
            ['email' => 'cafe@demo.test', 'name' => 'Café Central', 'phone' => '5551001003', 'type' => 'business', 'rfc' => 'CCA240101CD2'],
        ])->mapWithKeys(function (array $data): array {
            $user = User::updateOrCreate(
                ['email' => $data['email'], 'password' => 'password'],
                ['name' => $data['name'], 'status' => 'active', 'email_verified_at' => now()],
            );

            $user->assignRole('cliente');

            $customer = CustomerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'customer_type' => $data['type'],
                    'contact_name' => $data['name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                ],
            );


            CustomerAddress::updateOrCreate(
                ['customer_id' => $customer->id, 'label' => 'Principal'],
                [
                    'recipient_name' => $data['name'],
                    'phone' => $data['phone'],
                    'street' => 'Av. Reforma',
                    'exterior_number' => (string) (100 + $customer->id),
                    'neighborhood' => 'Centro',
                    'city' => 'Ciudad de México',
                    'municipality' => 'Cuauhtémoc',
                    'state' => 'CDMX',
                    'postal_code' => '06000',
                    'country' => 'México',
                    'is_default' => true,
                ],
            );

            if ($data['type'] === 'business') {
                TaxProfile::updateOrCreate(
                    ['customer_id' => $customer->id, 'rfc' => $data['rfc']],
                    [
                        'tax_person_type' => 'business',
                        'legal_name' => $data['name'] . ' S.A. de C.V.',
                        'fiscal_regime_code' => '601',
                        'fiscal_postal_code' => '06000',
                        'cfdi_use_code' => 'G03',
                        'fiscal_email' => $data['email'],
                        'is_default' => true,
                    ],
                );
            }

            return [$data['email'] => $customer];
        });

        $variants = DesignVariant::query()->with(['design', 'stickerSize', 'stickerFinish'])->get();
        $sales = [
            ['number' => 'ORD-DEMO-001', 'customer' => 'ana@demo.test', 'status' => 'in_production', 'variants' => [0, 2], 'quantities' => [25, 15], 'invoice' => false, 'paid' => 500, 'days' => 1],
            ['number' => 'ORD-DEMO-002', 'customer' => 'estudio@demo.test', 'status' => 'pending_deposit', 'variants' => [4, 6], 'quantities' => [50, 25], 'invoice' => true, 'paid' => 0, 'days' => 3],
            ['number' => 'ORD-DEMO-003', 'customer' => 'cafe@demo.test', 'status' => 'ready_for_pickup', 'variants' => [8], 'quantities' => [100], 'invoice' => true, 'paid' => 1250, 'days' => -1],
        ];

        foreach ($sales as $sale) {
            $customer = $customers[$sale['customer']];
            $address = $customer->addresses()->where('is_default', true)->first();
            $lineItems = collect($sale['variants'])->values()->map(function (int $variantIndex, int $lineIndex) use ($variants, $sale): array {
                $variant = $variants->get($variantIndex % $variants->count());
                $quantity = $sale['quantities'][$lineIndex];

                return [
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'line_total' => $quantity * (float) $variant->unit_price,
                ];
            });
            $subtotal = $lineItems->sum('line_total');
            $deposit = round($subtotal * 0.33, 2);
            $paid = min((float) $sale['paid'], $subtotal);

            $order = Order::updateOrCreate(
                ['order_number' => $sale['number']],
                [
                    'customer_id' => $customer->id,
                    'status' => $sale['status'],
                    'delivery_method' => $sale['status'] === 'ready_for_pickup' ? 'pickup' : 'shipping',
                    'pickup_location' => $sale['status'] === 'ready_for_pickup' ? 'Tienda Luna · Roma Norte' : null,
                    'shipping_address_id' => $sale['status'] === 'ready_for_pickup' ? null : $address?->id,
                    'requested_at' => now()->subDays(max(0, $sale['days'] + 2)),
                    'confirmed_at' => now()->subDays(max(0, $sale['days'] + 1)),
                    'production_started_at' => $sale['status'] === 'in_production' ? now()->subDay() : null,
                    'estimated_production_days' => 3,
                    'estimated_ready_at' => now()->addDays($sale['days'])->toDateString(),
                    'subtotal' => $subtotal,
                    'total_amount' => $subtotal,
                    'deposit_percentage' => 33,
                    'deposit_required' => $deposit,
                    'amount_paid' => $paid,
                    'balance_due' => max(0, $subtotal - $paid),
                    'customer_note' => 'Pedido de demostración para validar el flujo de ventas.',
                ],
            );

            foreach ($lineItems as $item) {
                $variant = $item['variant'];
                OrderItem::updateOrCreate(
                    ['order_id' => $order->id, 'design_variant_id' => $variant->id],
                    [
                        'design_name_snapshot' => $variant->design->name,
                        'size_snapshot' => $variant->stickerSize->name,
                        'width_cm_snapshot' => $variant->stickerSize->width_cm,
                        'height_cm_snapshot' => $variant->stickerSize->height_cm,
                        'finish_snapshot' => $variant->stickerFinish->name,
                        'quantity' => $item['quantity'],
                        'unit_price' => $variant->unit_price,
                        'line_total' => $item['line_total'],
                    ],
                );
            }

            if ($paid > 0) {
                Payment::updateOrCreate(
                    ['order_id' => $order->id, 'reference' => 'DEMO-' . $order->order_number],
                    ['received_by_user_id' => User::query()->where('email', config('admin.email'))->value('id'), 'method' => 'bank_transfer', 'status' => 'recorded', 'amount' => $paid, 'paid_at' => now()->subDay()],
                );
            }

            if ($sale['invoice']) {
                $taxProfile = $customer->taxProfiles()->where('is_default', true)->first();
                if ($taxProfile) {
                    OrderInvoiceRequest::updateOrCreate(
                        ['order_id' => $order->id],
                        ['tax_profile_id' => $taxProfile->id, 'status' => $sale['status'] === 'ready_for_pickup' ? 'issued' : 'requested', 'note' => 'Solicitud de demostración.'],
                    );
                }
            }
        }
    }
}
