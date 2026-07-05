<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_responders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('emergency_requests')->onDelete('cascade');
            $table->foreignId('responder_id')->constrained('responders')->onDelete('cascade');
            $table->foreignId('agency_id')->nullable()->constrained('emergency_agencies')->nullOnDelete();
            $table->enum('status', ['pending', 'accepted', 'responding', 'arrived', 'completed', 'rejected'])->default('pending');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('estimated_arrival')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['incident_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_responders');
    }
};