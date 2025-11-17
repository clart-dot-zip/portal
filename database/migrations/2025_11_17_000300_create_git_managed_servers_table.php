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
        Schema::create('git_managed_servers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pterodactyl_server_id');
            $table->uuid('pterodactyl_server_uuid');
            $table->string('pterodactyl_server_identifier');
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('pterodactyl_node_id')->nullable();
            $table->string('pterodactyl_node_name')->nullable();
            $table->string('container_name');
            $table->string('repository_path');
            $table->string('repository_url')->nullable();
            $table->string('default_branch')->default('main');
            $table->string('remote_name')->default('origin');
            $table->string('ssh_host')->nullable();
            $table->unsignedSmallInteger('ssh_port')->default(22);
            $table->string('ssh_username')->nullable();
            $table->string('ssh_private_key_path')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('pterodactyl_server_uuid');
            $table->index('pterodactyl_server_identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('git_managed_servers');
    }
};
