<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('badge_number')->nullable()->unique();
            $table->foreignId('agency_id')->nullable()->constrained('emergency_agencies')->nullOnDelete();
            $table->enum('status', ['available', 'busy', 'offline', 'on_duty'])->default('offline');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('current_location_address')->nullable();
            $table->enum('availability_status', ['available', 'busy', 'offline'])->default('offline');
            $table->string('service_type')->nullable();
            $table->string('vehicle_info')->nullable();
            $table->timestamp('shift_start')->nullable();
            $table->timestamp('shift_end')->nullable();
            $table->boolean('is_on_duty')->default(false);
            $table->timestamps();

            $table->unique('user_id');
            $table->index('status');
            $table->index('availability_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responders');
    }
};