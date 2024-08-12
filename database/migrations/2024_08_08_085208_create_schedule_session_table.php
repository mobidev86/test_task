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
        Schema::create('schedule_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_repeated')->default(false);
            $table->tinyInteger('rating')->unsigned()->nullable()->default(null)->comment('Rating from 1 to 10');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_sessions');
    }
};
