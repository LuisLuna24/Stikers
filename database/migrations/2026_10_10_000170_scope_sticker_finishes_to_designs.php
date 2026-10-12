<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sticker_finishes', function (Blueprint $table): void {
            $table->foreignId('design_id')->after('id')->constrained('designs')->cascadeOnDelete();
            $table->unique(['design_id', 'name'], 'sticker_finishes_design_name_unique');
        });

        if (Schema::hasIndex('sticker_finishes', 'sticker_finishes_name_unique')) {
            Schema::table('sticker_finishes', function (Blueprint $table): void {
                $table->dropUnique('sticker_finishes_name_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('sticker_finishes', function (Blueprint $table): void {
            $table->dropForeign(['design_id']);
            $table->dropUnique('sticker_finishes_design_name_unique');
            $table->dropColumn('design_id');
        });

        // A scoped catalog can contain the same finish name for multiple designs.
        // Do not make rollback fail (or delete data) when those duplicates exist.
        $hasDuplicateNames = DB::table('sticker_finishes')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (! $hasDuplicateNames) {
            Schema::table('sticker_finishes', function (Blueprint $table): void {
                $table->unique('name', 'sticker_finishes_name_unique');
            });
        }
    }
};
