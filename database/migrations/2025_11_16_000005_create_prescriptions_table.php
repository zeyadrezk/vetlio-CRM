<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create prescriptions table for tracking medications prescribed to patients.
     */
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('prescriber_id')->constrained('users')->comment('Doctor who prescribed');
            $table->foreignId('medical_document_id')->nullable()->constrained('medical_documents')
                ->onDelete('set null')->comment('Visit where medication was prescribed');
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');

            // Medication information
            $table->string('medication_name')->comment('Medication name (brand or generic)');
            $table->string('medication_code', 50)->nullable()->comment('NDC or other drug code');
            $table->string('dosage', 100)->comment('Dosage amount (e.g., "10mg", "5ml")');
            $table->string('form', 50)->nullable()->comment('Form: tablet, capsule, liquid, injection, etc.');
            $table->string('route', 50)->nullable()->comment('Route: oral, topical, IV, IM, etc.');

            // Instructions
            $table->string('frequency', 100)->comment('How often: "once daily", "twice daily", "every 4 hours", etc.');
            $table->string('duration', 100)->nullable()->comment('Duration: "7 days", "until gone", "ongoing"');
            $table->integer('quantity')->nullable()->comment('Quantity prescribed');
            $table->integer('refills')->default(0)->comment('Number of refills allowed');
            $table->text('instructions')->nullable()->comment('Additional instructions for patient');

            // Pharmacy
            $table->string('pharmacy')->nullable()->comment('Pharmacy name where sent');
            $table->string('pharmacy_phone', 20)->nullable()->comment('Pharmacy phone number');

            // Dates and status
            $table->date('prescribed_date')->comment('Date prescribed');
            $table->date('valid_until')->nullable()->comment('Expiration date for prescription');
            $table->timestamp('filled_date')->nullable()->comment('Date prescription was filled');
            $table->timestamp('discontinued_at')->nullable()->comment('Date medication was discontinued');
            $table->string('discontinuation_reason')->nullable()->comment('Reason for discontinuation');

            // Status and flags
            $table->enum('status', ['active', 'filled', 'expired', 'cancelled', 'discontinued'])->default('active');
            $table->boolean('controlled_substance')->default(false)->comment('Is this a controlled substance?');
            $table->string('dea_schedule', 10)->nullable()->comment('DEA schedule: I, II, III, IV, V');
            $table->boolean('generic_allowed')->default(true)->comment('Allow generic substitution');
            $table->boolean('send_electronically')->default(true)->comment('Send via e-prescribe');

            // Notes
            $table->text('notes')->nullable()->comment('Internal notes about prescription');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('patient_id');
            $table->index('prescriber_id');
            $table->index('medical_document_id');
            $table->index('status');
            $table->index('prescribed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
