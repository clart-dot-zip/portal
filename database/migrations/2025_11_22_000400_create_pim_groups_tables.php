<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pim_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('pim_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('default_duration_minutes')->default(15);
            $table->unsignedSmallInteger('min_duration_minutes')->default(5);
            $table->unsignedSmallInteger('max_duration_minutes')->default(60);
            $table->boolean('auto_approve')->default(false);
            $table->timestamps();
        });

        Schema::create('pim_group_permission', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pim_group_id')->constrained('pim_groups')->cascadeOnDelete();
            $table->foreignId('pim_permission_id')->constrained('pim_permissions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['pim_group_id', 'pim_permission_id'], 'pim_group_permission_unique');
        });

        Schema::create('pim_group_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pim_group_id')->constrained('pim_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['pim_group_id', 'user_id'], 'pim_group_user_unique');
        });

        Schema::table('pim_activations', function (Blueprint $table) {
            $table->foreignId('pim_group_id')->nullable()->after('user_id')->constrained('pim_groups')->cascadeOnDelete();
        });

        $permissionIds = $this->seedPermissions();
        $defaultGroupIds = $this->seedDefaultGroups($permissionIds);

        if (Schema::hasTable('pim_activations')) {
            $fallbackGroupId = $defaultGroupIds['git-management-operators'] ?? null;

            if ($fallbackGroupId) {
                DB::table('pim_activations')
                    ->whereNull('pim_group_id')
                    ->update(['pim_group_id' => $fallbackGroupId]);
            }
        }

        Schema::table('pim_activations', function (Blueprint $table) {
            if (Schema::hasColumn('pim_activations', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('pim_activations', 'server_username_snapshot')) {
                $table->dropColumn('server_username_snapshot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pim_activations', function (Blueprint $table) {
            $table->string('role')->nullable();
            $table->string('server_username_snapshot')->nullable();
            if (Schema::hasColumn('pim_activations', 'pim_group_id')) {
                $table->dropConstrainedForeignId('pim_group_id');
            }

            $table->index(['role', 'status'], 'pim_activations_role_status_index');
        });

        Schema::dropIfExists('pim_group_user');
        Schema::dropIfExists('pim_group_permission');
        Schema::dropIfExists('pim_groups');
        Schema::dropIfExists('pim_permissions');
    }

    /**
     * @return array<string, int>
     */
    private function seedPermissions(): array
    {
        $now = now();
        $permissions = config('pim.permissions', []);
        $ids = [];

        foreach ($permissions as $key => $details) {
            $existing = DB::table('pim_permissions')->where('key', $key)->first();

            if ($existing) {
                $ids[$key] = $existing->id;
                DB::table('pim_permissions')->where('id', $existing->id)->update([
                    'label' => $details['label'] ?? Str::title(str_replace('.', ' ', $key)),
                    'description' => $details['description'] ?? null,
                    'updated_at' => $now,
                ]);
                continue;
            }

            $ids[$key] = DB::table('pim_permissions')->insertGetId([
                'key' => $key,
                'label' => $details['label'] ?? Str::title(str_replace('.', ' ', $key)),
                'description' => $details['description'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $ids;
    }

    /**
     * @param  array<string, int>  $permissionIds
     * @return array<string, int>
     */
    private function seedDefaultGroups(array $permissionIds): array
    {
        $now = now();
        $groups = config('pim.default_groups', []);
        $ids = [];

        foreach ($groups as $slug => $definition) {
            $slug = Str::slug($slug);
            $existing = DB::table('pim_groups')->where('slug', $slug)->first();

            if ($existing) {
                DB::table('pim_groups')->where('id', $existing->id)->update([
                    'name' => $definition['name'] ?? Str::headline($slug),
                    'description' => $definition['description'] ?? null,
                    'default_duration_minutes' => $definition['default_duration_minutes'] ?? 15,
                    'min_duration_minutes' => $definition['min_duration_minutes'] ?? 5,
                    'max_duration_minutes' => $definition['max_duration_minutes'] ?? 60,
                    'auto_approve' => $definition['auto_approve'] ?? false,
                    'updated_at' => $now,
                ]);
                $groupId = $existing->id;
            } else {
                $groupId = DB::table('pim_groups')->insertGetId([
                    'name' => $definition['name'] ?? Str::headline($slug),
                    'slug' => $slug,
                    'description' => $definition['description'] ?? null,
                    'default_duration_minutes' => $definition['default_duration_minutes'] ?? 15,
                    'min_duration_minutes' => $definition['min_duration_minutes'] ?? 5,
                    'max_duration_minutes' => $definition['max_duration_minutes'] ?? 60,
                    'auto_approve' => $definition['auto_approve'] ?? false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $ids[$slug] = $groupId;

            $permissionKeys = $definition['permissions'] ?? [];
            foreach ($permissionKeys as $permissionKey) {
                $permissionId = $permissionIds[$permissionKey] ?? null;

                if (!$permissionId) {
                    continue;
                }

                $exists = DB::table('pim_group_permission')
                    ->where('pim_group_id', $groupId)
                    ->where('pim_permission_id', $permissionId)
                    ->exists();

                if (!$exists) {
                    DB::table('pim_group_permission')->insert([
                        'pim_group_id' => $groupId,
                        'pim_permission_id' => $permissionId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        return $ids;
    }
};
