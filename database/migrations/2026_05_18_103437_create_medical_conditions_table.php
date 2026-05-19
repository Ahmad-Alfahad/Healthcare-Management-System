<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', [
                'allergy',
                'chronic',
            ]);
            $table->text('notes')
                ->nullable();

            $table->timestamps();

                        $table->unique([
                'name',
                'type'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_conditions');
    }
};
