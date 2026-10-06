<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bracket_id')->constrained('brackets')->cascadeOnDelete();
            $table->unsignedTinyInteger('round');
            $table->unsignedInteger('match_number'); // position within the round

            $table->foreignId('team1_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('team2_id')->nullable()->constrained('teams')->nullOnDelete();

            $table->unsignedInteger('team1_score')->nullable();
            $table->unsignedInteger('team2_score')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('teams')->nullOnDelete();

            $table->dateTime('scheduled_at')->nullable();
            $table->string('venue')->nullable();

            // pending | scheduled | ongoing | completed | tied | skipped
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
