<?php

use App\Livewire\Modules\Customer\Catalog as CustomerCatalog;
use App\Livewire\Modules\Admin\Stikers\Form;
use App\Models\CustomerProfile;
use App\Models\Design;
use App\Models\DesignRequest;
use App\Models\DesignVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StickerFinish;
use App\Models\StickerSize;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function stickerSize(): StickerSize
{
    return StickerSize::create(['name' => fake()->unique()->word(), 'width_cm' => 5, 'height_cm' => 5, 'is_active' => true]);
}

function validVariant(StickerSize $size, string $finishKey = 'new-0', bool $active = true): array
{
    return ['sticker_size_id' => $size->id, 'finish_key' => $finishKey, 'unit_price' => '12.50', 'minimum_quantity' => 5, 'quantity_increment' => 5, 'is_active' => $active];
}

it('saves incomplete designs as inactive drafts and publishes only complete designs', function () {
    Storage::fake('public');

    Livewire::test(Form::class)
        ->set('sku', 'STK-DRAFT')
        ->set('name', 'Diseño borrador')
        ->call('saveDraft')
        ->assertHasNoErrors();

    expect(Design::first()->is_active)->toBeFalse();

    $size = stickerSize();

    Livewire::test(Form::class)
        ->set('sku', 'STK-PUBLISHED')
        ->set('name', 'Diseño publicado')
        ->set('preview_image', UploadedFile::fake()->image('preview.png'))
        ->set('vector_file', UploadedFile::fake()->create('design.svg', 1, 'image/svg+xml'))
        ->set('newFinishes', [['name' => 'Mate', 'description' => '', 'image' => UploadedFile::fake()->image('finish.png')]])
        ->set('variants', [validVariant($size)])
        ->call('publish')
        ->assertHasNoErrors();

    expect(Design::where('sku', 'STK-PUBLISHED')->value('is_active'))->toBeTrue();
    expect(DesignVariant::where('sticker_size_id', $size->id)->value('minimum_quantity'))->toBe(5);
});

it('rejects duplicate combinations and invalid quantity settings', function () {
    $size = stickerSize();
    $variant = validVariant($size);
    $variant['minimum_quantity'] = 10;

    Livewire::test(Form::class)
        ->set('sku', 'STK-INVALID')
        ->set('name', 'Diseño inválido')
        ->set('variants', [$variant, validVariant($size)])
        ->call('saveDraft')
        ->assertHasErrors(['variants.0.minimum_quantity']);

    Livewire::test(Form::class)
        ->set('sku', 'STK-DUPLICATE')
        ->set('name', 'Diseño duplicado')
        ->set('newFinishes', [['name' => 'Mate', 'description' => '', 'image' => null]])
        ->set('variants', [validVariant($size), validVariant($size)])
        ->call('saveDraft')
        ->assertHasErrors(['variants']);
});

it('keeps inactive designs out of the customer catalog and accepts design requests', function () {
    $user = User::factory()->create();
    CustomerProfile::create(['user_id' => $user->id, 'customer_type' => 'individual', 'contact_name' => 'Cliente', 'phone' => '5555555555', 'email' => $user->email]);
    $size = stickerSize();
    $activeDesign = Design::create(['sku' => 'STK-ACTIVE', 'name' => 'Visible', 'slug' => 'visible', 'vector_file_url' => 'design-vectors/a.svg', 'preview_image_url' => 'design-previews/a.png', 'is_active' => true]);
    $activeFinish = StickerFinish::create(['design_id' => $activeDesign->id, 'name' => 'Mate', 'is_active' => true]);
    $activeDesign->variants()->create(validVariant($size, 'finish-'.$activeFinish->id));
    $inactiveDesign = Design::create(['sku' => 'STK-INACTIVE', 'name' => 'Oculto', 'slug' => 'oculto', 'vector_file_url' => 'design-vectors/b.svg', 'is_active' => false]);
    $inactiveFinish = StickerFinish::create(['design_id' => $inactiveDesign->id, 'name' => 'Brillante', 'is_active' => true]);
    $inactiveDesign->variants()->create(validVariant($size, 'finish-'.$inactiveFinish->id, false));

    $this->actingAs($user);

    Livewire::test(CustomerCatalog::class)
        ->call('saveRequest')
        ->assertHasErrors(['title', 'design_reference_url'])
        ->set('title', 'Diseño especial')
        ->set('design_reference_url', 'https://example.com/reference')
        ->set('requested_quantity', 10)
        ->call('saveRequest')
        ->assertHasNoErrors();

    expect(DesignRequest::count())->toBe(1);
    expect(Design::active()->whereHas('variants', fn ($query) => $query->active())->count())->toBe(1);
});

it('preserves order snapshots when a catalog price changes', function () {
    $size = stickerSize();
    $design = Design::create(['sku' => 'STK-SNAPSHOT', 'name' => 'Snapshot', 'slug' => 'snapshot', 'vector_file_url' => 'design-vectors/s.svg']);
    $finish = StickerFinish::create(['design_id' => $design->id, 'name' => 'Mate', 'is_active' => true]);
    $variant = $design->variants()->create(validVariant($size, 'finish-'.$finish->id));
    $user = User::factory()->create();
    $customer = CustomerProfile::create(['user_id' => $user->id, 'customer_type' => 'individual', 'contact_name' => 'Cliente', 'phone' => '5555555555', 'email' => $user->email]);
    $order = Order::create(['order_number' => 'ORD-TEST-1', 'customer_id' => $customer->id, 'requested_at' => now()]);
    $item = OrderItem::create(['order_id' => $order->id, 'design_variant_id' => $variant->id, 'design_name_snapshot' => $design->name, 'size_snapshot' => $size->name, 'width_cm_snapshot' => 5, 'height_cm_snapshot' => 5, 'finish_snapshot' => $finish->name, 'quantity' => 5, 'unit_price' => 12.50, 'line_total' => 62.50]);

    $variant->update(['unit_price' => 20]);

    expect($item->fresh()->unit_price)->toBe('12.50');
});
