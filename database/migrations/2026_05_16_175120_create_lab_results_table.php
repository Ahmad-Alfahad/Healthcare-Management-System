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
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lab_request_item_id")->constrained();
            $table->foreignId("lab_staff_id")->constrained()->references('id')->on('lab_staff');
            $table->string("notes")->nullable();
            $table->decimal("value", 10, 2);
            $table->string("unit");
            $table->string("reference_range");
            $table->string("access_token")->nullable();
            $table->dateTime("completed_at")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};
