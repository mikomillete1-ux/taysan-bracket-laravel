<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brackets', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 // e.g. "Barangay Taysan Basketball League 2026"
            $table->string('sport_type')->default('basketball');
            $table->string('format')->default('single_elimination'); // single_elimination | round_robin
            $table->unsignedTinyInteger('total_rounds')->default(0);
            $table->string('status')->default('draft'); // draft | ongoing | completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brackets');
    }
};
