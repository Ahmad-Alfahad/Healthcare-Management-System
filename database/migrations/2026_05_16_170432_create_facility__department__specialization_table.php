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
        Schema::create('facility_department_specialization', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("facility_department_id");
            $table->foreignId("specialization_id")->constrained();
            $table->timestamps();

            $table->foreign("facility_department_id" , "fa_dept_spec_fa_dept")->references("id")->on("facility_department");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility__department__specializations');
    }
};
