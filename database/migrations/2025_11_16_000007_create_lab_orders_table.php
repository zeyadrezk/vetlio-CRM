<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create lab_orders table for tracking laboratory test orders and results.
     */
    public function up(): void
    {
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('ordered_by')->constrained('users')->comment('Doctor who ordered the test');
            $table->foreignId('medical_document_id')->nullable()->constrained('medical_documents')
                ->onDelete('set null')->comment('Visit where test was ordered');
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');

            // Test information
            $table->string('test_name')->comment('Name of the laboratory test');
            $table->string('test_code', 50)->nullable()->comment('CPT or LOINC code');
            $table->string('test_category', 100)->nullable()->comment('Category: Chemistry, Hematology, Microbiology, etc.');

            // Priority and timing
            $table->enum('priority', ['routine', 'stat', 'urgent', 'asap'])->default('routine')
                ->comment('Test priority level');
            $table->timestamp('ordered_at')->useCurrent()->comment('When test was ordered');
            $table->timestamp('collected_at')->nullable()->comment('When specimen was collected');
            $table->timestamp('resulted_at')->nullable()->comment('When results were received');

            // Status
            $table->enum('status', [
                'ordered',
                'specimen_collected',
                'in_progress',
                'completed',
                'cancelled',
                'pending_review',
                'reviewed'
            ])->default('ordered')->comment('Current status of lab order');

            // Results
            $table->text('result_value')->nullable()->comment('Test result value');
            $table->string('result_unit', 50)->nullable()->comment('Unit of measurement');
            $table->string('reference_range', 200)->nullable()->comment('Normal reference range');
            $table->enum('abnormal_flag', ['normal', 'high', 'low', 'critical_high', 'critical_low', 'abnormal'])->nullable()
                ->comment('Flag for abnormal results');

            // Laboratory information
            $table->string('performing_lab')->nullable()->comment('Laboratory that performed the test');
            $table->string('specimen_type', 100)->nullable()->comment('Type of specimen: blood, urine, swab, etc.');
            $table->string('specimen_id', 100)->nullable()->comment('Specimen/accession number');

            // Review and interpretation
            $table->foreignId('reviewed_by')->nullable()->constrained('users')
                ->comment('Doctor who reviewed results');
            $table->timestamp('reviewed_at')->nullable()->comment('When results were reviewed');
            $table->text('interpretation')->nullable()->comment('Doctor interpretation of results');

            // Notes
            $table->text('clinical_notes')->nullable()->comment('Clinical indication for test');
            $table->text('lab_notes')->nullable()->comment('Notes from laboratory');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('patient_id');
            $table->index('ordered_by');
            $table->index('medical_document_id');
            $table->index('status');
            $table->index('ordered_at');
            $table->index(['patient_id', 'ordered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_orders');
    }
};
