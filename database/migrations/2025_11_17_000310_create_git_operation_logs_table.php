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
        Schema::create('git_operation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('git_managed_server_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->text('command');
            $table->json('parameters')->nullable();
            $table->enum('status', ['pending', 'running', 'succeeded', 'failed'])->default('pending');
            $table->longText('output')->nullable();
            $table->longText('error_output')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('git_operation_logs');
    }
};
