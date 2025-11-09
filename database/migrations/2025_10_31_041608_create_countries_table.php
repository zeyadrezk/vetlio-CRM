<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('iso2', 2)->unique();   // "HR"
            $table->string('iso3', 3)->unique();   // "HRV"
            $table->string('name_en');             // "Croatia"
            $table->string('name_native');         // "Hrvatska"
            $table->unsignedInteger('currency_id');
            $table->unsignedInteger('default_language_id');
            $table->string('phone_code', 8)->nullable(); // "385"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
