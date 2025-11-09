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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('code')->nullable();
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('payment_method_id');
            $table->string('transaction_id')->nullable();
            $table->unsignedInteger('client_id');
            $table->text('note')->nullable();
            $table->timestamp('payment_at');
            $table->integer('amount');
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
        Schema::dropIfExists('payments');
    }
};
