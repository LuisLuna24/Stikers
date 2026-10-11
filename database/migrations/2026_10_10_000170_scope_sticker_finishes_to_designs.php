<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sticker_finishes', function (Blueprint $table): void {
            $table->foreignId('design_id')->after('id')->constrained('designs')->cascadeOnDelete();
            $table->dropUnique('sticker_finishes_name_unique');
            $table->unique(['design_id', 'name'], 'sticker_finishes_design_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sticker_finishes', function (Blueprint $table): void {
            $table->dropUnique('sticker_finishes_design_name_unique');
            $table->dropForeign(['design_id']);
            $table->dropColumn('design_id');
            $table->unique('name', 'sticker_finishes_name_unique');
        });
    }
};
