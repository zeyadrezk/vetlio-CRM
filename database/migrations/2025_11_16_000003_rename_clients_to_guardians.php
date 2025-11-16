<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rename clients table to guardians and update all foreign key references
     * throughout the system. This supports the guardian model where one person
     * (guardian) can manage multiple patients (e.g., parent managing children).
     */
    public function up(): void
    {
        // Step 1: Rename the main table
        Schema::rename('clients', 'guardians');

        // Step 2: Rename foreign key columns in patients table
        Schema::table('patients', function (Blueprint $table) {
            $table->renameColumn('client_id', 'guardian_id');
        });

        // Step 3: Rename foreign key columns in reservations table
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('client_id', 'guardian_id');
        });

        // Step 4: Rename foreign key columns in medical_documents table
        Schema::table('medical_documents', function (Blueprint $table) {
            $table->renameColumn('client_id', 'guardian_id');
        });

        // Step 5: Rename foreign key columns in invoices table
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('client_id', 'guardian_id');
        });

        // Step 6: Rename foreign key columns in payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('client_id', 'guardian_id');
        });

        // Note: config/auth.php 'client' guard must be manually updated to 'guardian'
        // Also update FilamentPanelProvider configurations
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse all column renames (in reverse order)
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('guardian_id', 'client_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('guardian_id', 'client_id');
        });

        Schema::table('medical_documents', function (Blueprint $table) {
            $table->renameColumn('guardian_id', 'client_id');
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('guardian_id', 'client_id');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->renameColumn('guardian_id', 'client_id');
        });

        // Rename table back
        Schema::rename('guardians', 'clients');
    }
};
