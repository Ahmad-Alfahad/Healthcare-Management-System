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
        Schema::create('pharmacists', function (Blueprint $table) {
            $table->id();
            $table->foreignId("employee_id")->references("id")->on("employees")
            ->unique()
            ->constrained()->onDelete('cascade');
            $table->string("degree"); 
            $table->unsignedTinyInteger("years_of_experience")->default(0); 
            $table->string("license_number")->nullable()->unique(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacists');
    }
};