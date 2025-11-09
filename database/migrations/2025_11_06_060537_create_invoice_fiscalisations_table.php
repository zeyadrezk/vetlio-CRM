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
        Schema::create('invoice_fiscalisations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id');
            $table->string('zki')->nullable();
            $table->string('jir')->nullable();
            $table->text('qrcode')->nullable();
            $table->longText('request_xml')->nullable();
            $table->longText('response_xml')->nullable();
            $table->string('status')->default('pending'); // pending|success|error
            $table->text('error_message')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_fiscalisations');
    }
};
