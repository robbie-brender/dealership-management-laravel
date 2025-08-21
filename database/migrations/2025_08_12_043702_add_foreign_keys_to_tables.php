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
        // Add foreign keys to appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreign('dealership_id')->references('id')->on('dealerships')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
        
        // Add foreign keys to interactions table
        Schema::table('interactions', function (Blueprint $table) {
            $table->foreign('dealership_id')->references('id')->on('dealerships')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('knowledge_base_id')->references('id')->on('knowledge_bases')->onDelete('set null');
        });
        
        // Add foreign keys to knowledge_bases table
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->foreign('dealership_id')->references('id')->on('dealerships')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys from appointments table
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['dealership_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['user_id']);
        });
        
        // Drop foreign keys from interactions table
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropForeign(['dealership_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['knowledge_base_id']);
        });
        
        // Drop foreign keys from knowledge_bases table
        Schema::table('knowledge_bases', function (Blueprint $table) {
            $table->dropForeign(['dealership_id']);
            $table->dropForeign(['user_id']);
        });
    }
};
