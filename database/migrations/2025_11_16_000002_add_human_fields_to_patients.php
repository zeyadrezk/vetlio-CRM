<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add human-specific medical and demographic fields to patients table.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Biometric data
            $table->string('blood_type', 5)->nullable()->after('date_of_birth')
                ->comment('Blood type: A+, A-, B+, B-, O+, O-, AB+, AB-');
            $table->decimal('height', 5, 2)->nullable()->after('blood_type')
                ->comment('Height in centimeters');
            $table->decimal('weight', 5, 2)->nullable()->after('height')
                ->comment('Weight in kilograms');
            $table->decimal('bmi', 4, 2)->nullable()->after('weight')
                ->comment('Body Mass Index (calculated)');

            // Identification
            $table->string('ssn', 20)->nullable()->after('bmi')
                ->comment('Social Security Number / National ID');
            $table->string('mrn', 50)->nullable()->unique()->after('ssn')
                ->comment('Medical Record Number (unique identifier)');

            // Insurance information
            $table->string('insurance_provider')->nullable()->after('mrn')
                ->comment('Insurance company name');
            $table->string('insurance_number', 100)->nullable()->after('insurance_provider')
                ->comment('Insurance policy/member number');
            $table->string('insurance_group', 100)->nullable()->after('insurance_number')
                ->comment('Insurance group number');

            // Emergency contact
            $table->string('emergency_contact_name')->nullable()->after('insurance_group')
                ->comment('Emergency contact full name');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name')
                ->comment('Emergency contact phone number');
            $table->string('emergency_contact_relation', 50)->nullable()->after('emergency_contact_phone')
                ->comment('Relationship to patient');

            // Medical information
            $table->string('primary_care_physician')->nullable()->after('emergency_contact_relation')
                ->comment('Primary care physician name');
            $table->json('medical_history')->nullable()->after('primary_care_physician')
                ->comment('Structured medical history (chronic conditions, surgeries, etc.)');
            $table->json('current_medications')->nullable()->after('medical_history')
                ->comment('Current medications list');
            $table->json('chronic_conditions')->nullable()->after('current_medications')
                ->comment('Chronic medical conditions');
            $table->text('allergies_medications')->nullable()->after('chronic_conditions')
                ->comment('Drug allergies (separate from general allergies field)');

            // Demographics
            $table->string('marital_status', 20)->nullable()->after('allergies_medications')
                ->comment('Marital status: single, married, divorced, widowed');
            $table->string('occupation')->nullable()->after('marital_status')
                ->comment('Patient occupation');
            $table->string('preferred_pharmacy')->nullable()->after('occupation')
                ->comment('Preferred pharmacy for prescriptions');
            $table->boolean('advance_directives')->default(false)->after('preferred_pharmacy')
                ->comment('Has living will/DNR/AND orders');
            $table->string('language_preference', 10)->nullable()->default('en')->after('advance_directives')
                ->comment('Preferred language for communication');
        });

        // Rename pet-specific fields to human equivalents
        Schema::table('patients', function (Blueprint $table) {
            $table->renameColumn('dangerous', 'special_needs');
            $table->renameColumn('dangerous_note', 'special_needs_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename back to pet-specific fields
        Schema::table('patients', function (Blueprint $table) {
            $table->renameColumn('special_needs', 'dangerous');
            $table->renameColumn('special_needs_note', 'dangerous_note');
        });

        // Remove all human-specific fields
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'blood_type',
                'height',
                'weight',
                'bmi',
                'ssn',
                'mrn',
                'insurance_provider',
                'insurance_number',
                'insurance_group',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'primary_care_physician',
                'medical_history',
                'current_medications',
                'chronic_conditions',
                'allergies_medications',
                'marital_status',
                'occupation',
                'preferred_pharmacy',
                'advance_directives',
                'language_preference',
            ]);
        });
    }
};
