<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create diagnoses table for tracking patient diagnoses with ICD-10 codes.
     */
    public function up(): void
    {
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medical_document_id')->nullable()->constrained('medical_documents')
                ->onDelete('set null')->comment('Visit where diagnosis was made');
            $table->foreignId('diagnosed_by')->constrained('users')->comment('Doctor who made diagnosis');
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');

            // Diagnosis information
            $table->string('icd10_code', 10)->comment('ICD-10 diagnosis code');
            $table->string('icd10_description')->comment('ICD-10 code description');
            $table->text('clinical_description')->nullable()->comment('Clinical description in doctor own words');

            // Classification
            $table->enum('type', ['primary', 'secondary', 'differential', 'rule_out', 'chronic'])->default('primary')
                ->comment('Type of diagnosis');
            $table->enum('category', [
                'acute',
                'chronic',
                'acute_on_chronic',
                'history_of',
                'suspected',
                'confirmed'
            ])->default('confirmed')->comment('Diagnosis category');

            // Dates
            $table->date('diagnosed_date')->comment('Date diagnosis was made');
            $table->date('onset_date')->nullable()->comment('Date symptoms began');
            $table->date('resolved_date')->nullable()->comment('Date diagnosis was resolved');

            // Status
            $table->enum('status', ['active', 'resolved', 'chronic', 'in_remission', 'recurrent'])->default('active')
                ->comment('Current status of diagnosis');
            $table->enum('severity', ['mild', 'moderate', 'severe', 'critical'])->nullable()
                ->comment('Severity of condition');

            // Billing
            $table->boolean('billable')->default(true)->comment('Is this diagnosis billable');
            $table->integer('dx_order')->nullable()->comment('Order on claim form (1=primary, 2=secondary, etc.)');

            // Clinical details
            $table->text('treatment_plan')->nullable()->comment('Treatment plan for this diagnosis');
            $table->text('notes')->nullable()->comment('Additional clinical notes');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('patient_id');
            $table->index('medical_document_id');
            $table->index('icd10_code');
            $table->index('status');
            $table->index('diagnosed_date');
            $table->index(['patient_id', 'status']);
            $table->index(['patient_id', 'diagnosed_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnoses');
    }
};
