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
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('client_id');
            $table->unsignedInteger('service_id');
            $table->unsignedInteger('branch_id');
            $table->unsignedInteger('service_provider_id')->nullable();
            $table->unsignedInteger('patient_id');
            $table->date('date');
            $table->timestamp('from');
            $table->timestamp('to');
            $table->string('note')->nullable();
            $table->string('reason_for_coming')->nullable();
            $table->unsignedTinyInteger('approval_status_id')->default(1); //Request
            $table->timestamp('approval_at')->nullable();
            $table->string('approval_note')->nullable();
            $table->unsignedInteger('approval_by')->nullable();
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
        Schema::dropIfExists('appointment_requests');
    }
};
