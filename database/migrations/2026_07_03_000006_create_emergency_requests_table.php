<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('emergency_type_id')->constrained()->onDelete('restrict');
            $table->enum('status', ['pending', 'accepted', 'responding', 'arrived', 'completed', 'cancelled', 'rejected'])->default('pending');

            // Required by App\Models\EmergencyRequest (generated in booted())
            $table->string('incident_number', 30)->unique();

            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_sos')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->text('verification_note')->nullable();
            $table->foreignId('assigned_dispatcher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('estimated_arrival')->nullable();
            $table->timestamp('actual_arrival')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('is_sos');
            $table->index('created_at');
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_requests');
    }
};