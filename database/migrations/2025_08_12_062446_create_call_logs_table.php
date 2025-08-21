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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('call_id')->unique()->comment('Unique call ID from Vapi');
            $table->string('status')->default('initiated')->comment('Call status: initiated, in-progress, completed, failed');
            $table->string('direction')->default('inbound')->comment('Call direction: inbound or outbound');
            $table->string('caller_number')->nullable()->comment('Phone number of the caller');
            $table->string('recipient_number')->nullable()->comment('Phone number being called');
            $table->unsignedInteger('duration')->default(0)->comment('Call duration in seconds');
            $table->string('assistant_id')->nullable()->comment('ID of the AI assistant handling the call');
            $table->text('transcript')->nullable()->comment('Call transcript');
            $table->json('metadata')->nullable()->comment('Additional call metadata');
            $table->timestamp('call_started_at')->nullable();
            $table->timestamp('call_ended_at')->nullable();
            $table->timestamps();
            
            // Foreign key to dealership
            $table->foreignId('dealership_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
