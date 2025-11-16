<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create immunizations table for tracking patient vaccination records.
     */
    public function up(): void
    {
        Schema::create('immunizations', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('administered_by')->nullable()->constrained('users')
                ->comment('Healthcare provider who administered vaccine');
            $table->foreignId('medical_document_id')->nullable()->constrained('medical_documents')
                ->onDelete('set null')->comment('Visit where vaccine was given');
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');

            // Vaccine information
            $table->string('vaccine_name')->comment('Vaccine name (e.g., "COVID-19", "Influenza")');
            $table->string('vaccine_code', 50)->nullable()->comment('CVX code (CDC vaccine code)');
            $table->string('vaccine_product', 200)->nullable()->comment('Brand/product name');

            // Manufacturer details
            $table->string('manufacturer')->nullable()->comment('Vaccine manufacturer');
            $table->string('lot_number', 100)->nullable()->comment('Vaccine lot number');
            $table->date('expiration_date')->nullable()->comment('Vaccine expiration date');

            // Administration details
            $table->date('administered_date')->comment('Date vaccine was administered');
            $table->string('dose_amount', 50)->nullable()->comment('Dose amount (e.g., "0.5 mL")');
            $table->integer('dose_number')->nullable()->comment('Dose number in series (1, 2, 3, etc.)');
            $table->integer('series_total')->nullable()->comment('Total doses in series');

            // Administration site and route
            $table->string('administration_site', 100)->nullable()
                ->comment('Body site: left deltoid, right deltoid, left thigh, etc.');
            $table->enum('route', [
                'intramuscular',
                'subcutaneous',
                'intradermal',
                'oral',
                'intranasal',
                'other'
            ])->nullable()->comment('Route of administration');

            // VIS (Vaccine Information Statement)
            $table->string('vis_version')->nullable()->comment('VIS version date');
            $table->date('vis_provided_date')->nullable()->comment('Date VIS was provided to patient');

            // Follow-up
            $table->date('next_due_date')->nullable()->comment('Date next dose is due');
            $table->text('next_dose_instructions')->nullable()->comment('Instructions for next dose');

            // Consent and refusal
            $table->boolean('consent_obtained')->default(true)->comment('Was consent obtained');
            $table->foreignId('consent_signed_by')->nullable()->constrained('users')
                ->comment('Who signed consent (if applicable)');
            $table->boolean('refused')->default(false)->comment('Was vaccine refused');
            $table->text('refusal_reason')->nullable()->comment('Reason for refusal if applicable');

            // Adverse reactions
            $table->boolean('adverse_reaction_reported')->default(false)
                ->comment('Was an adverse reaction reported');
            $table->text('adverse_reaction_description')->nullable()
                ->comment('Description of adverse reaction');

            // Reporting
            $table->boolean('reported_to_registry')->default(false)
                ->comment('Reported to immunization registry');
            $table->string('funding_source')->nullable()
                ->comment('Vaccine funding source: private, VFC, etc.');

            // Notes
            $table->text('notes')->nullable()->comment('Additional notes');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('patient_id');
            $table->index('administered_date');
            $table->index('next_due_date');
            $table->index(['patient_id', 'administered_date']);
            $table->index(['patient_id', 'vaccine_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunizations');
    }
};
