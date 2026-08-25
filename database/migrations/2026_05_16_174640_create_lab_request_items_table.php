<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lab_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("visit_id")->constrained();
            $table->foreignId("lab_test_id")->constrained();
            $table->dateTime("requested_at");
            $table->string("notes");
            $table->enum("status", ["pending", "completed", "processing", "cancelled"])->default("pending");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_request_items');
    }
};
