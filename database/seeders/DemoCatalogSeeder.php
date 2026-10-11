<?php

namespace Database\Seeders;

use App\Models\Design;
use App\Models\DesignVariant;
use App\Models\StickerFinish;
use App\Models\StickerSize;
use Illuminate\Database\Seeder;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $sizes = collect([
            ['name' => 'Pequeño', 'width_cm' => 5, 'height_cm' => 5],
            ['name' => 'Mediano', 'width_cm' => 8, 'height_cm' => 8],
            ['name' => 'Grande', 'width_cm' => 12, 'height_cm' => 12],
        ])->mapWithKeys(fn (array $size): array => [
            $size['name'] => StickerSize::updateOrCreate(
                ['name' => $size['name']],
                [...$size, 'is_active' => true],
            ),
        ]);

        $designs = [
            [
                'sku' => 'STK-LUNA-001',
                'name' => 'Luna sonriente',
                'slug' => 'luna-sonriente',
                'description' => 'Sticker ilustrado de Luna con acabado brillante.',
                'preview_image_url' => 'https://placehold.co/800x800/e0e7ff/3730a3?text=Luna',
                'finishes' => ['Brillante', 'Mate'],
                'prices' => [85, 110],
            ],
            [
                'sku' => 'STK-FLOR-002',
                'name' => 'Flor silvestre',
                'slug' => 'flor-silvestre',
                'description' => 'Diseño floral para papelería, empaques y regalos.',
                'preview_image_url' => 'https://placehold.co/800x800/fce7f3/9d174d?text=Flor',
                'finishes' => ['Transparente', 'Holográfico'],
                'prices' => [95, 135],
            ],
            [
                'sku' => 'STK-CAFE-003',
                'name' => 'Café de especialidad',
                'slug' => 'cafe-de-especialidad',
                'description' => 'Sticker con identidad artesanal para cafeterías.',
                'preview_image_url' => 'https://placehold.co/800x800/fef3c7/92400e?text=Cafe',
                'finishes' => ['Mate', 'Vinil resistente'],
                'prices' => [105, 145],
            ],
        ];

        foreach ($designs as $designData) {
            $design = Design::updateOrCreate(
                ['sku' => $designData['sku']],
                [
                    'name' => $designData['name'],
                    'slug' => $designData['slug'],
                    'description' => $designData['description'],
                    'vector_file_url' => "https://example.com/designs/{$designData['slug']}.svg",
                    'preview_image_url' => $designData['preview_image_url'],
                    'is_active' => true,
                ],
            );

            foreach ($designData['finishes'] as $finishIndex => $finishName) {
                $finish = StickerFinish::updateOrCreate(
                    ['design_id' => $design->id, 'name' => $finishName],
                    ['description' => "Acabado {$finishName} para {$design->name}.", 'is_active' => true],
                );

                foreach ([$sizes['Pequeño'], $sizes['Mediano']] as $size) {
                    DesignVariant::updateOrCreate(
                        [
                            'design_id' => $design->id,
                            'sticker_size_id' => $size->id,
                            'sticker_finish_id' => $finish->id,
                        ],
                        [
                            'unit_price' => $designData['prices'][$finishIndex] + ($size->name === 'Mediano' ? 25 : 0),
                            'minimum_quantity' => 5,
                            'quantity_increment' => 5,
                            'is_active' => true,
                        ],
                    );
                }
            }
        }
    }
}
