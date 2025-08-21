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
            $table->string('title');
            $table->date('date');
            $table->time('start_time');
            $table->integer('duration')->comment('Duration in minutes');
            $table->enum('status', ['scheduled', 'confirmed', 'cancelled', 'completed', 'rescheduled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->string('location')->nullable();
            $table->unsignedBigInteger('dealership_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id')->nullable();
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
