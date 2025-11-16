<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create insurance_plans table to track patient insurance coverage.
     * Supports multiple insurance plans per patient (primary, secondary, tertiary).
     */
    public function up(): void
    {
        Schema::create('insurance_plans', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('organisation_id')->constrained('organisations')->onDelete('cascade');

            // Insurance provider information
            $table->string('provider_name')->comment('Insurance company name');
            $table->string('policy_number', 100)->comment('Policy/member number');
            $table->string('group_number', 100)->nullable()->comment('Group number');

            // Subscriber information (person who owns the policy)
            $table->string('subscriber_name')->nullable()->comment('Policy holder name');
            $table->string('subscriber_relationship', 50)->nullable()->comment('Relationship to patient: self, spouse, child, other');
            $table->date('subscriber_dob')->nullable()->comment('Subscriber date of birth');

            // Coverage dates
            $table->date('effective_date')->nullable()->comment('Coverage start date');
            $table->date('expiration_date')->nullable()->comment('Coverage end date');

            // Financial details
            $table->decimal('copay_amount', 10, 2)->nullable()->comment('Standard copay amount');
            $table->decimal('deductible', 10, 2)->nullable()->comment('Annual deductible');
            $table->decimal('deductible_met', 10, 2)->default(0)->comment('Amount of deductible met this year');
            $table->decimal('out_of_pocket_max', 10, 2)->nullable()->comment('Annual out-of-pocket maximum');
            $table->decimal('out_of_pocket_met', 10, 2)->default(0)->comment('Amount of OOP met this year');

            // Priority and status
            $table->enum('priority', ['primary', 'secondary', 'tertiary'])->default('primary')
                ->comment('Insurance priority order');
            $table->enum('plan_type', ['commercial', 'medicare', 'medicaid', 'tricare', 'other'])->default('commercial')
                ->comment('Type of insurance plan');
            $table->enum('verification_status', ['not_verified', 'verified', 'inactive', 'expired'])->default('not_verified')
                ->comment('Verification status');
            $table->timestamp('last_verified_at')->nullable()->comment('Last verification date');

            // Additional information
            $table->text('notes')->nullable()->comment('Additional notes about coverage');
            $table->boolean('active')->default(true)->comment('Is this insurance plan active');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('patient_id');
            $table->index('policy_number');
            $table->index(['patient_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_plans');
    }
};
