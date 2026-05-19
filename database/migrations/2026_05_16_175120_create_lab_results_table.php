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
            $table->foreignId("lab_staff_id")->constraiend();
            $table->string("notes");
            $table->enum("status" , ["pending" , "done"]);
            $table->integer("value");
            $table->string("unit");
            $table->integer("reference_range");
            $table->string("access_token");
            $table->dateTime("completed_at");
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
