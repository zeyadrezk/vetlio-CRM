<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rename service_provider references to attending_physician throughout the system.
     * This updates column names to better reflect human healthcare terminology.
     */
    public function up(): void
    {
        // Rename in reservations table
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('service_provider_id', 'attending_physician_id');
        });

        // Rename in medical_documents table
        Schema::table('medical_documents', function (Blueprint $table) {
            $table->renameColumn('service_provider_id', 'attending_physician_id');
        });

        // Rename boolean column in users table
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('service_provider', 'is_physician');
        });

        // Update link table name
        Schema::rename('link_service_users', 'link_service_physicians');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the link table rename
        Schema::rename('link_service_physicians', 'link_service_users');

        // Reverse users table column
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('is_physician', 'service_provider');
        });

        // Reverse medical_documents table column
        Schema::table('medical_documents', function (Blueprint $table) {
            $table->renameColumn('attending_physician_id', 'service_provider_id');
        });

        // Reverse reservations table column
        Schema::table('reservations', function (Blueprint $table) {
            $table->renameColumn('attending_physician_id', 'service_provider_id');
        });
    }
};
