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
        Schema::create('team_task_work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_task_id')->constrained('team_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('worked_minutes'); // 例: 30, 90 など
            $table->date('worked_at');                 // その日の作業として記録
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['team_task_id', 'worked_at']);
            $table->index(['user_id', 'worked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_task_work_logs');
    }
};
