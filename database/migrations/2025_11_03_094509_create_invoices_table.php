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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique()->default(Str::uuid());
            $table->string('code');
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('issuer_id');
            $table->unsignedInteger('branch_id');
            $table->date('invoice_date')->nullable();
            $table->date('invoice_due_date')->nullable();
            $table->unsignedInteger('price_list_id');

            $table->unsignedInteger('storno_of_id')->nullable();
            $table->unsignedInteger('storno_user_id')->nullable();
            $table->text('client_note')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->unsignedTinyInteger('payment_method_id');
            $table->unsignedInteger('card_id')->nullable();
            $table->unsignedInteger('bank_account_id')->nullable();

            $table->integer('total_base_price')->default(0);
            $table->integer('total_discount')->default(0);
            $table->integer('total_tax')->default(0);
            $table->integer('total')->default(0);

            $table->string('zki')->nullable();
            $table->string('jir')->nullable();
            $table->text('qrcode')->nullable();
            $table->timestamp('fiscalization_at')->nullable();

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
        Schema::dropIfExists('invoices');
    }
};
