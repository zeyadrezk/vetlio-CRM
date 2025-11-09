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
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('uuid')->unique();
            $table->string('subdomain')->unique();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip_code')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('language_id')->nullable();
            $table->unsignedInteger('currency_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->boolean('active')->default(true);
            $table->string('oib')->nullable();
            $table->boolean('in_vat_system')->default(false);

            //Fiscalisation
            $table->boolean('fiscalization_enabled')->default(false);
            $table->boolean('fiscalization_demo')->default(true);
            $table->char('sequence_mark')->nullable();
            $table->string('certificate_path')->nullable();
            $table->text('certificate_password')->nullable();
            $table->date('certificate_valid_to')->nullable();
            $table->json('certificate_details')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisations');
    }
};
