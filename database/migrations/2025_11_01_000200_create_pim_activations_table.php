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
        Schema::create('pim_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->unsignedSmallInteger('duration_minutes');
            $table->timestamp('activated_at');
            $table->timestamp('expires_at');
            $table->timestamp('deactivated_at')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason');
            $table->text('deactivation_reason')->nullable();
            $table->text('status_message')->nullable();
            $table->string('server_username_snapshot')->nullable();
            $table->timestamps();

            $table->index(['role', 'status']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pim_activations');
    }
};
