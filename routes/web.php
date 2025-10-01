<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthentikController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Socialite::driver('authentik')->redirect();
    })->name('login');
    Route::get('/auth/callback', [AuthController::class, 'callback']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/onboard', [UserController::class, 'onboard'])->name('onboard');
        Route::post('/onboard', [UserController::class, 'processOnboard'])->name('onboard.process');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/sync', [UserController::class, 'sync'])->name('sync');
    });
    
    // Group Management Routes
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('index');
        Route::get('/{id}', [GroupController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [GroupController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GroupController::class, 'update'])->name('update');
        Route::post('/sync', [GroupController::class, 'sync'])->name('sync');
        Route::post('/{id}/users', [GroupController::class, 'addUser'])->name('add-user');
        Route::delete('/{id}/users/{userId}', [GroupController::class, 'removeUser'])->name('remove-user');
    });
    
    // Application Management Routes
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [ApplicationController::class, 'index'])->name('index');
        Route::get('/debug', function() {
            try {
                $authentik = new \App\Services\Authentik\AuthentikSDK(config('services.authentik.api_token'));
                $result = $authentik->request('GET', '/core/applications/');
                return response()->json([
                    'success' => true,
                    'total' => count($result['results'] ?? []),
                    'sample_app' => !empty($result['results']) ? $result['results'][0] : null
                ]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        })->name('debug');
        Route::get('/debug-policies/{id}', function($id) {
            try {
                $authentik = new \App\Services\Authentik\AuthentikSDK(config('services.authentik.api_token'));
                
                // Try different endpoints to understand what's available
                $debug = [
                    'application_id' => $id,
                    'endpoints_tested' => []
                ];
                
                // Test application details
                try {
                    $app = $authentik->request('GET', "/core/applications/{$id}/");
                    $debug['application'] = $app;
                    $debug['endpoints_tested']['get_app'] = ['success' => true];
                } catch (\Exception $e) {
                    $debug['endpoints_tested']['get_app'] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
                
                // Test update with minimal data
                try {
                    $testUpdate = $authentik->request('PATCH', "/core/applications/{$id}/", [
                        'name' => 'test-name'  // Try updating just the name
                    ]);
                    $debug['endpoints_tested']['patch_app'] = ['success' => true, 'result' => $testUpdate];
                } catch (\Exception $e) {
                    $debug['endpoints_tested']['patch_app'] = [
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
                
                return response()->json($debug);
                
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        })->name('debug-policies');
        Route::get('/debug-app-update/{id}', function($id) {
            try {
                $authentik = new \App\Services\Authentik\AuthentikSDK(config('services.authentik.api_token'));
                
                // Get current app data first
                $currentApp = $authentik->request('GET', "/core/applications/{$id}/");
                
                // Try updating with group assignment
                $updateData = array_merge($currentApp, [
                    'group' => '76c306a5-e245-4edc-8218-cff70fae1bd8'  // Use the group ID from logs
                ]);
                
                $result = $authentik->request('PUT', "/core/applications/{$id}/", $updateData);
                
                return response()->json([
                    'success' => true,
                    'current_app' => $currentApp,
                    'update_result' => $result
                ]);
                
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        })->name('debug-app-update');
        Route::get('/{id}', [ApplicationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ApplicationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ApplicationController::class, 'update'])->name('update');
        Route::post('/{id}/groups', [ApplicationController::class, 'assignGroupAccess'])->name('assign-group');
        Route::post('/{id}/users', [ApplicationController::class, 'assignUserAccess'])->name('assign-user');
        Route::delete('/{id}/access', [ApplicationController::class, 'removeAccess'])->name('remove-access');
    });
    
    // Authentik Management Routes
    Route::prefix('authentik')->name('authentik.')->group(function () {
        Route::get('/applications', [AuthentikController::class, 'applications'])->name('applications');
        Route::get('/applications/{id}', [AuthentikController::class, 'application'])->name('application');
        Route::post('/applications', [AuthentikController::class, 'createApplication'])->name('applications.create');
        
        Route::get('/users', [AuthentikController::class, 'users'])->name('users');
        Route::get('/users/search', [AuthentikController::class, 'searchUsers'])->name('users.search');
        Route::post('/users', [AuthentikController::class, 'createUser'])->name('users.create');
        Route::get('/users/{userId}/groups', [AuthentikController::class, 'userGroups'])->name('users.groups');
        Route::post('/users/{userId}/groups', [AuthentikController::class, 'addUserToGroup'])->name('users.groups.add');
        
        Route::get('/groups', [AuthentikController::class, 'groups'])->name('groups');
        Route::get('/groups/{groupId}/members', [AuthentikController::class, 'groupMembers'])->name('groups.members');
        
        Route::get('/providers', [AuthentikController::class, 'providers'])->name('providers');
        Route::get('/providers/oauth', [AuthentikController::class, 'oauthProviders'])->name('providers.oauth');
        
        Route::get('/flows', [AuthentikController::class, 'flows'])->name('flows');
        Route::get('/flows/auth', [AuthentikController::class, 'authFlows'])->name('flows.auth');
    });
});
