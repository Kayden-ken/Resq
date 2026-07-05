<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'])->default('Unknown');
            $table->json('allergies')->nullable();
            $table->json('medical_conditions')->nullable();
            $table->json('medications')->nullable();
            $table->text('medical_notes')->nullable();
            $table->boolean('organ_donor')->default(false);
            $table->string('emergency_medical_id')->nullable();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_infos');
    }
};