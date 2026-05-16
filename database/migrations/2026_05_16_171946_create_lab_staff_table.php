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
        Schema::create('lab_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId("facility_id")->constrained();
            $table->foreignId("profile_id")->constrained();
            $table->string("specialiaztion");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_staff');
    }
};
