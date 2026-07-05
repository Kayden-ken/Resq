<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emergency_requests', function (Blueprint $table) {
            // Add only if it doesn't already exist (safe for re-runs)
            if (!Schema::hasColumn('emergency_requests', 'incident_number')) {
                $table->string('incident_number', 30)->unique()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('emergency_requests', function (Blueprint $table) {
            if (Schema::hasColumn('emergency_requests', 'incident_number')) {
                $table->dropColumn('incident_number');
            }
        });
    }
};

