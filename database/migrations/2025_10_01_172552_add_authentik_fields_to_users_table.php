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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('authentik_id');
            $table->boolean('is_active')->default(true)->after('username');
            $table->timestamp('last_login')->nullable()->after('is_active');
            $table->json('authentik_attributes')->nullable()->after('last_login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'authentik_id',
                'username',
                'is_active',
                'last_login',
                'authentik_attributes'
            ]);
        });
    }
};
