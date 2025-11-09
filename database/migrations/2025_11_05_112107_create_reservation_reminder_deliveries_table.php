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
        Schema::create('reservation_reminder_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_reminder_id')->constrained()->cascadeOnDelete();
            $table->string('channel');
            $table->string('status')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_reminder_deliveries');
    }
};
