<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('emergency_requests')->onDelete('cascade');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('incident_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_history');
    }
};