<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profile_id')->unique()->constrained()->onDelete('cascade');
            $table->enum('blood_type', [
                'A+',
                'A-',
                'B+',
                'B-',
                'AB+',
                'AB-',
                'O+',
                'O-'
            ])->nullable();
            $table->string('emergency_contact_name')->nullable(); // اسم شخص للطوارئ
            $table->string('emergency_contact_phone')->nullable(); // رقم هاتف الطوارئ
            $table->string('emergency_contact_relation')->nullable(); // صلة القرابة (أب، زوج...)


            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
