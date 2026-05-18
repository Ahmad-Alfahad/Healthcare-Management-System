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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId("facility_department_specialization_id")->references("id")->on("facility_department_specialization");
            $table->foreignId("profile_id")->constrained();
            $table->string('qualification'); // (like: MD, PhD, Master's)
            $table->unsignedTinyInteger('years_of_experience')->default(0); 
            $table->text('biography')->nullable(); 
            $table->text('achievements')->nullable(); 
            $table->string('languages')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
