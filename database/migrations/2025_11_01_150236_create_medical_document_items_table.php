<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_document_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_document_id');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->morphs('priceable');
            $table->unsignedInteger('service_provider_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('price')->default(0);
            $table->integer('base_price')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('tax')->default(0);
            $table->integer('total')->default(0);
            $table->unsignedInteger('organisation_id');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_document_items');
    }
};
