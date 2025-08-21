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
        Schema::table('call_logs', function (Blueprint $table) {
            $table->json('vapi_summary')->nullable()->after('metadata');
            $table->boolean('vapi_success_evaluation')->nullable()->after('vapi_summary');
            $table->json('vapi_analysis')->nullable()->after('vapi_success_evaluation');
            $table->string('vapi_recording_url')->nullable()->after('vapi_analysis');
            $table->string('vapi_stereo_recording_url')->nullable()->after('vapi_recording_url');
            $table->decimal('vapi_cost', 8, 4)->nullable()->after('vapi_stereo_recording_url');
            $table->integer('vapi_duration_seconds')->nullable()->after('vapi_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('call_logs', function (Blueprint $table) {
            $table->dropColumn([
                'vapi_summary',
                'vapi_success_evaluation',
                'vapi_analysis',
                'vapi_recording_url',
                'vapi_stereo_recording_url',
                'vapi_cost',
                'vapi_duration_seconds'
            ]);
        });
    }
};
