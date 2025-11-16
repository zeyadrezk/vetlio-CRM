<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create vital_signs table for recording patient vital signs at each visit.
     */
    public function up(): void
    {
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('medical_document_id')->nullable()->constrained('medical_documents')
                ->onDelete('cascade')->comment('Visit where vitals were recorded');
            $table->foreignId('measured_by')->nullable()->constrained('users')
                ->comment('User who measured vitals (nurse, MA, doctor)');
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');

            // Measurement timestamp
            $table->timestamp('measured_at')->useCurrent()->comment('When vitals were measured');

            // Vital signs measurements
            $table->integer('blood_pressure_systolic')->nullable()->comment('Systolic BP in mmHg');
            $table->integer('blood_pressure_diastolic')->nullable()->comment('Diastolic BP in mmHg');
            $table->integer('heart_rate')->nullable()->comment('Heart rate in beats per minute');
            $table->integer('respiratory_rate')->nullable()->comment('Respiratory rate in breaths per minute');
            $table->decimal('temperature', 4, 2)->nullable()->comment('Temperature in Celsius');
            $table->enum('temperature_route', ['oral', 'axillary', 'tympanic', 'temporal', 'rectal'])->nullable()
                ->comment('Method of temperature measurement');
            $table->integer('oxygen_saturation')->nullable()->comment('SpO2 percentage (0-100)');

            // Body measurements
            $table->decimal('height', 5, 2)->nullable()->comment('Height in centimeters');
            $table->decimal('weight', 5, 2)->nullable()->comment('Weight in kilograms');
            $table->decimal('bmi', 4, 2)->nullable()->comment('Body Mass Index (calculated)');
            $table->decimal('head_circumference', 5, 2)->nullable()->comment('Head circumference in cm (for pediatrics)');

            // Pain assessment
            $table->integer('pain_level')->nullable()->comment('Pain scale 0-10 (0=no pain, 10=worst pain)');
            $table->string('pain_location')->nullable()->comment('Location of pain if reported');

            // Additional measurements
            $table->integer('blood_glucose')->nullable()->comment('Blood glucose in mg/dL');
            $table->integer('peak_flow')->nullable()->comment('Peak expiratory flow (for asthma patients)');

            // Context and notes
            $table->enum('patient_position', ['sitting', 'standing', 'lying', 'other'])->nullable()
                ->comment('Patient position during measurement');
            $table->text('notes')->nullable()->comment('Additional notes about vital signs');

            // Flags for abnormal values
            $table->boolean('flagged_abnormal')->default(false)->comment('Any values outside normal range');

            $table->timestamps();

            // Indexes
            $table->index('patient_id');
            $table->index('medical_document_id');
            $table->index('measured_at');
            $table->index(['patient_id', 'measured_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
