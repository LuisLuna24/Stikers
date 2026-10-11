<?php

namespace App\Livewire\Modules\Customer\Purchase;

use App\Models\Design;
use App\Models\DesignVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class Form extends Component
{
    /** @var array<int|string, int|string> */
    public array $cart = [];

    public function mount(): void
    {
        $this->cart = session()->get($this->draftSessionKey(), []);
    }

    public function addToCart(int $variantId, mixed $quantity): void
    {
        $variant = $this->activeVariant($variantId);
        $quantity = $this->parseQuantity($quantity);

        if (! $variant) {
            $this->addError('cart', 'Esta variante ya no está disponible. Actualiza la página e inténtalo de nuevo.');

            return;
        }

        if ($quantity === null) {
            $this->addError('cart', 'La cantidad debe ser un número entero.');

            return;
        }

        if (! $this->quantityIsValid($variant, $quantity)) {
            $this->addError('cart', $this->quantityError($variant));

            return;
        }

        $currentQuantity = $this->parseQuantity($this->cart[$variantId] ?? 0);
        if ($currentQuantity === null) {
            $this->addError('cart', 'Corrige la cantidad existente antes de agregar más stickers.');

            return;
        }

        $newQuantity = $currentQuantity + $quantity;

        if (! $this->quantityIsValid($variant, $newQuantity)) {
            $this->addError('cart', 'La cantidad acumulada debe conservar múltiplos de '.$variant->quantity_increment.'.');

            return;
        }

        $this->cart[$variantId] = $newQuantity;
        $this->persistCart();
        $this->resetErrorBag('cart');
        Flux::toast(variant: 'success', heading: 'Agregado al carrito', text: $variant->design->name.' ya está en tu resumen.');
    }

    public function updateCartQuantity(int $variantId, mixed $quantity): void
    {
        $variant = $this->activeVariant($variantId);
        $quantity = $this->parseQuantity($quantity);

        if (! $variant) {
            $this->removeFromCart($variantId);

            return;
        }

        if ($quantity === null) {
            $this->addError('cart', 'La cantidad debe ser un número entero.');

            return;
        }

        if (! $this->quantityIsValid($variant, $quantity)) {
            $this->addError('cart', $this->quantityError($variant));

            return;
        }

        $this->cart[$variantId] = $quantity;
        $this->persistCart();
        $this->resetErrorBag('cart');
    }

    public function removeFromCart(int $variantId): void
    {
        unset($this->cart[$variantId]);
        $this->persistCart();
        $this->resetErrorBag('cart');
    }

    public function saveDraft(): void
    {
        if ($this->cart === []) {
            $this->addError('cart', 'Agrega al menos un sticker antes de guardar el borrador.');

            return;
        }

        if (! $this->cartIsValid()) {
            return;
        }

        $this->persistCart();
        Flux::toast(variant: 'success', heading: 'Borrador guardado', text: 'Puedes volver más tarde y continuar tu cotización.');
    }

    public function sendQuote(): void
    {
        if ($this->cart === []) {
            $this->addError('cart', 'Agrega al menos un sticker antes de enviar la cotización.');

            return;
        }

        $variants = $this->cartVariants();
        if ($variants->count() !== count($this->cart) || ! $this->cartIsValid($variants)) {
            return;
        }

        $customer = Auth::user()?->customerProfile;
        if (! $customer) {
            $this->addError('cart', 'Tu cuenta todavía no tiene un perfil de cliente. Contacta al equipo.');

            return;
        }

        $subtotal = $variants->sum(function (DesignVariant $variant): float {
            return (float) $variant->unit_price * (int) $this->cart[$variant->id];
        });
        $deposit = round($subtotal * 0.33, 2);

        DB::transaction(function () use ($customer, $variants, $subtotal, $deposit): void {
            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'customer_id' => $customer->id,
                'status' => 'pending_deposit',
                'delivery_method' => 'pickup',
                'requested_at' => now(),
                'subtotal' => $subtotal,
                'total_amount' => $subtotal,
                'deposit_percentage' => 33,
                'deposit_required' => $deposit,
                'amount_paid' => 0,
                'balance_due' => $subtotal,
            ]);

            foreach ($variants as $variant) {
                $quantity = (int) $this->cart[$variant->id];

                OrderItem::create([
                    'order_id' => $order->id,
                    'design_variant_id' => $variant->id,
                    'design_name_snapshot' => $variant->design->name,
                    'size_snapshot' => $variant->stickerSize->name,
                    'width_cm_snapshot' => $variant->stickerSize->width_cm,
                    'height_cm_snapshot' => $variant->stickerSize->height_cm,
                    'finish_snapshot' => $variant->stickerFinish->name,
                    'quantity' => $quantity,
                    'unit_price' => $variant->unit_price,
                    'line_total' => $quantity * (float) $variant->unit_price,
                ]);
            }
        });

        $this->cart = [];
        session()->forget($this->draftSessionKey());
        Flux::toast(variant: 'success', heading: 'Cotización enviada', text: 'El equipo revisará tu pedido y te contactará con los siguientes pasos.');
    }

    public function render()
    {
        $designs = Design::query()
            ->active()
            ->whereHas('variants', fn ($query) => $query->active()
                ->whereHas('stickerSize', fn ($sizeQuery) => $sizeQuery->where('is_active', true))
                ->whereHas('stickerFinish', fn ($finishQuery) => $finishQuery->where('is_active', true)))
            ->with(['variants' => fn ($query) => $query->active()
                ->whereHas('stickerSize', fn ($sizeQuery) => $sizeQuery->where('is_active', true))
                ->whereHas('stickerFinish', fn ($finishQuery) => $finishQuery->where('is_active', true))
                ->with(['stickerSize', 'stickerFinish'])])
            ->orderBy('name')
            ->get();

        $catalogData = $designs->mapWithKeys(fn (Design $design): array => [
            $design->id => [
                'variants' => $design->variants->map(fn (DesignVariant $variant): array => [
                    'id' => $variant->id,
                    'size_id' => $variant->sticker_size_id,
                    'size_name' => $variant->stickerSize->name,
                    'size_dimensions' => $variant->stickerSize->width_cm.' × '.$variant->stickerSize->height_cm.' cm',
                    'finish_id' => $variant->sticker_finish_id,
                    'finish_name' => $variant->stickerFinish->name,
                    'finish_description' => $variant->stickerFinish->description,
                    'price' => (float) $variant->unit_price,
                    'minimum_quantity' => $variant->minimum_quantity,
                    'quantity_increment' => $variant->quantity_increment,
                ])->values()->all(),
                'sizes' => $design->variants->map(fn (DesignVariant $variant): array => [
                    'id' => $variant->sticker_size_id,
                    'name' => $variant->stickerSize->name,
                    'dimensions' => $variant->stickerSize->width_cm.' × '.$variant->stickerSize->height_cm.' cm',
                ])->unique('id')->values()->all(),
                'finishes' => $design->variants->map(fn (DesignVariant $variant): array => [
                    'id' => $variant->sticker_finish_id,
                    'name' => $variant->stickerFinish->name,
                    'description' => $variant->stickerFinish->description,
                ])->unique('id')->values()->all(),
            ],
        ])->all();

        $cartItems = $this->cartVariants()->map(fn (DesignVariant $variant): array => [
            'variant' => $variant,
            'quantity' => (int) $this->cart[$variant->id],
            'subtotal' => (int) $this->cart[$variant->id] * (float) $variant->unit_price,
        ])->values();

        return view('livewire.modules.customer.purchase.form', [
            'designs' => $designs,
            'catalogData' => $catalogData,
            'cartItems' => $cartItems,
            'cartItemCount' => $cartItems->count(),
            'cartTotal' => $cartItems->sum('subtotal'),
        ]);
    }

    private function activeVariant(int $variantId): ?DesignVariant
    {
        return DesignVariant::query()
            ->active()
            ->whereKey($variantId)
            ->whereHas('design', fn ($query) => $query->active())
            ->whereHas('stickerSize', fn ($query) => $query->where('is_active', true))
            ->whereHas('stickerFinish', fn ($query) => $query->where('is_active', true))
            ->with(['design', 'stickerSize', 'stickerFinish'])
            ->first();
    }

    /** @return Collection<int, DesignVariant> */
    private function cartVariants(): Collection
    {
        $variantIds = collect(array_keys($this->cart))
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->values();

        if ($variantIds->isEmpty()) {
            return collect();
        }

        $cartOrder = array_map('intval', array_keys($this->cart));

        return DesignVariant::query()
            ->active()
            ->whereIn('id', $variantIds)
            ->whereHas('design', fn ($query) => $query->active())
            ->whereHas('stickerSize', fn ($query) => $query->where('is_active', true))
            ->whereHas('stickerFinish', fn ($query) => $query->where('is_active', true))
            ->with(['design', 'stickerSize', 'stickerFinish'])
            ->get()
            ->sortBy(fn (DesignVariant $variant): int => array_search($variant->id, $cartOrder, true) ?: 0)
            ->values();
    }

    private function cartIsValid(?Collection $variants = null): bool
    {
        $variants ??= $this->cartVariants();

        if ($variants->count() !== count($this->cart)) {
            $this->addError('cart', 'Uno de los stickers seleccionados ya no está disponible.');

            return false;
        }

        foreach ($variants as $variant) {
            $quantity = $this->parseQuantity($this->cart[$variant->id] ?? null);

            if ($quantity === null || ! $this->quantityIsValid($variant, $quantity)) {
                $this->addError('cart', $variant->design->name.': '.$this->quantityError($variant));

                return false;
            }
        }

        return true;
    }

    private function quantityIsValid(DesignVariant $variant, int $quantity): bool
    {
        return $quantity >= $variant->minimum_quantity
            && ($quantity - $variant->minimum_quantity) % $variant->quantity_increment === 0;
    }

    private function quantityError(DesignVariant $variant): string
    {
        return 'La cantidad mínima es de '.$variant->minimum_quantity.' stickers y debe aumentar en múltiplos de '.$variant->quantity_increment.'.';
    }

    private function parseQuantity(mixed $quantity): ?int
    {
        if (is_int($quantity)) {
            return $quantity;
        }

        if (is_string($quantity) && preg_match('/^\d+$/', $quantity) === 1) {
            return (int) $quantity;
        }

        return null;
    }

    private function persistCart(): void
    {
        $this->cart = collect($this->cart)
            ->mapWithKeys(fn ($quantity, $variantId): array => [(int) $variantId => max(0, (int) $quantity)])
            ->filter(fn (int $quantity): bool => $quantity > 0)
            ->all();

        session()->put($this->draftSessionKey(), $this->cart);
    }

    private function draftSessionKey(): string
    {
        return 'customer.purchase.draft.'.(Auth::id() ?? 'guest');
    }

    private function nextOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function previewUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        return Str::startsWith($url, ['http://', 'https://'])
            ? $url
            : Storage::disk('public')->url($url);
    }
}
