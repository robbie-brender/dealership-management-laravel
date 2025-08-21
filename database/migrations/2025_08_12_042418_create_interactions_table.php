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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['call', 'email', 'chat', 'in_person', 'other']);
            $table->dateTime('start_time');
            $table->integer('duration')->comment('Duration in minutes');
            $table->text('notes')->nullable();
            $table->text('summary')->nullable();
            $table->unsignedBigInteger('dealership_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('knowledge_base_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
