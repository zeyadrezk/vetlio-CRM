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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('date');                          // Datum blagdana
            $table->date('observed_date')->nullable();     // Ako se "observed" razlikuje
            $table->boolean('fixed')->default(false);      // Uvijek isti datum?
            $table->boolean('global')->default(true);
            $table->unsignedSmallInteger('launch_year')->nullable();
            $table->string('type', 32)->nullable();        // npr. "Public", "Bank"...
            $table->string('provider_uid', 64);            // stabilan ključ za upsert (npr. hash)
            $table->timestamps();

            $table->unique(['country_id', 'provider_uid']); // sprječava duplikate kroz više sync-ova
            $table->index(['country_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
