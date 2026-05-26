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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId("patient_id")->constrained();
            $table->foreignId("doctor_id")->constrained();
            $table->enum("status" , ["pending" , "confirmed" , "cancelled" , "completed"]);
            $table->string('reason');
            $table->dateTime("scheduled_date");
            $table->dateTime("start_time");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
