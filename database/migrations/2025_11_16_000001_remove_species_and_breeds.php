<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * IMPORTANT: This migration removes pet-specific tables (species and breeds)
     * as part of the transformation from Vetlio (pet clinic) to Coodely Hospital (human clinic).
     */
    public function up(): void
    {
        // Step 1: Remove foreign keys from patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['species_id']);
            $table->dropForeign(['breed_id']);
        });

        // Step 2: Remove columns from patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'species_id',     // Animal species (Dog, Cat, etc.)
                'breed_id',       // Animal breed (Labrador, Persian, etc.)
                'color',          // Fur/coat color (not relevant for humans)
            ]);
        });

        // Step 3: Drop breed and species tables entirely
        Schema::dropIfExists('breeds');
        Schema::dropIfExists('species');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate species table
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->softDeletes();
            $table->timestamps();
        });

        // Recreate breeds table
        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('species_id');
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('species_id')->references('id')->on('species')->onDelete('cascade');
        });

        // Re-add columns to patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->smallInteger('species_id')->unsigned()->nullable()->after('gender_id');
            $table->smallInteger('breed_id')->unsigned()->nullable()->after('species_id');
            $table->string('color')->nullable()->after('breed_id');

            $table->foreign('species_id')->references('id')->on('species')->onDelete('set null');
            $table->foreign('breed_id')->references('id')->on('breeds')->onDelete('set null');
        });
    }
};
