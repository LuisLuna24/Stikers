<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer_profiles')->cascadeOnDelete();
            $table->enum('tax_person_type', ['individual', 'business']);
            $table->string('legal_name', 200);
            $table->string('rfc', 13);
            $table->string('fiscal_regime_code', 10);
            $table->string('fiscal_postal_code', 10);
            $table->string('cfdi_use_code', 10);
            $table->string('fiscal_email', 190)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_profiles');
    }
};
