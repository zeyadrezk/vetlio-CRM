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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('color')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('client_id')->nullable();
            $table->smallInteger('gender_id')->nullable();
            $table->smallInteger('species_id')->unsigned()->nullable();
            $table->smallInteger('breed_id')->unsigned()->nullable();
            $table->boolean('dangerous')->default(false)->nullable();
            $table->string('dangerous_note')->nullable();
            $table->text('remarks')->nullable();
            $table->string('allergies')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->string('archived_note')->nullable();
            $table->unsignedInteger('archived_by')->nullable();
            $table->integer('organisation_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
