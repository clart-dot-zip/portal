<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthentikController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PimController;
use App\Http\Controllers\GitManagementController;
use App\Http\Controllers\GitManagedServerController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'portal.admin:false'])->name('dashboard');

// Test loading page (only in development)
if (app()->environment('local')) {
    Route::get('/test-loading', function () {
        return view('test-loading');
    })->middleware(['auth'])->name('test-loading');
}

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

// Admin Dashboard Route
Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->middleware(['auth', 'portal.admin:true'])->name('admin.dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return Socialite::driver('authentik')->redirect();
    })->name('login');
    Route::get('/auth/callback', [AuthController::class, 'callback']);
});

Route::middleware('auth')->group(function () {
    // User Profile Routes - Available to all authenticated users
    Route::get('/profile', [UserController::class, 'profile'])->middleware('portal.admin:false')->name('users.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->middleware('portal.admin:false')->name('users.profile.update');
});

// Portal Admin Routes - Require admin access
Route::middleware(['auth', 'portal.admin:true'])->group(function () {
    // PIM Dashboard Routes - Admin access required
    Route::prefix('pim')->name('pim.')->group(function () {
        Route::get('/', [PimController::class, 'index'])->name('index');
    });
    
    
    // User Management Routes - Admin access required
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/onboard', [UserController::class, 'onboard'])->name('onboard');
        Route::post('/onboard', [UserController::class, 'processOnboard'])->name('onboard.process');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/sync', [UserController::class, 'sync'])->name('sync');
        Route::post('/{id}/toggle-admin', [UserController::class, 'togglePortalAdmin'])->name('toggle-admin');
        Route::post('/{id}/send-recovery', [UserController::class, 'sendPasswordRecovery'])->name('send-recovery');

        Route::prefix('{id}/pim')->name('pim.')->group(function () {
            Route::post('/activate', [PimController::class, 'activate'])->name('activate');
            Route::post('/activations/{activation}/deactivate', [PimController::class, 'deactivate'])
                ->whereNumber('activation')
                ->name('deactivate');
        });
    });
    
    // Group Management Routes - Admin access required
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('index');
        Route::get('/create', [GroupController::class, 'create'])->name('create');
        Route::post('/', [GroupController::class, 'store'])->name('store');
        Route::get('/{id}', [GroupController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [GroupController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GroupController::class, 'update'])->name('update');
        Route::post('/sync', [GroupController::class, 'sync'])->name('sync');
        Route::post('/{id}/users', [GroupController::class, 'addUser'])->name('add-user');
        Route::delete('/{id}/users/{userId}', [GroupController::class, 'removeUser'])->name('remove-user');
    });
    
    // Application Management Routes - Admin access required
    Route::prefix('applications')->name('applications.')->group(function () {
        Route::get('/', [ApplicationController::class, 'index'])->name('index');
        Route::get('/debug', function() {
            try {
                $authentik = new \App\Services\Authentik\AuthentikSDK(config('services.authentik.api_token'));
                $result = $authentik->request('GET', '/core/applications/');
                dd($result);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        });
        Route::get('/{id}', [ApplicationController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [ApplicationController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ApplicationController::class, 'update'])->name('update');
        Route::post('/sync', [ApplicationController::class, 'sync'])->name('sync');
        Route::post('/{id}/groups', [ApplicationController::class, 'assignGroupAccess'])->name('assign-group');
        Route::delete('/{id}/groups/{groupId}', [ApplicationController::class, 'removeGroupAccess'])->name('remove-group');
        Route::post('/{id}/users', [ApplicationController::class, 'addUserAccess'])->name('add-user');
        Route::delete('/{id}/access', [ApplicationController::class, 'removeAccess'])->name('remove-access');
    });

    // Git Management Routes - Admin access required
    Route::prefix('git-management')->name('git-management.')->group(function () {
        Route::get('/', [GitManagementController::class, 'index'])->name('index');
        Route::get('/add', [GitManagedServerController::class, 'create'])->name('add');
        Route::post('/add', [GitManagedServerController::class, 'store'])->name('store');
        Route::post('/{server}/command', [GitManagementController::class, 'runCommand'])->name('command');
        Route::delete('/{server}', [GitManagedServerController::class, 'destroy'])->name('destroy');
    });

    // Cache management routes
    Route::prefix('cache')->name('cache.')->group(function () {
        Route::post('/clear', function () {
            \Illuminate\Support\Facades\Cache::flush();
            return redirect()->back()->with('success', 'Cache cleared successfully');
        })->name('clear');
        
        Route::post('/clear-user/{userId}', function ($userId) {
            \App\Services\DashboardCacheService::clearUserCache($userId);
            return redirect()->back()->with('success', 'User cache cleared successfully');
        })->name('clear-user');
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Debug routes for development; only register when debug mode is enabled
if (\config('app.debug')) {
    Route::middleware('auth')->group(function () {
        Route::prefix('debug/authentik')->name('authentik.')->group(function () {
            Route::get('/users', [AuthentikController::class, 'users'])->name('users');
            Route::get('/users/{userId}/groups', [AuthentikController::class, 'userGroups'])->name('users.groups');
            Route::post('/users/{userId}/groups', [AuthentikController::class, 'addUserToGroup'])->name('users.groups.add');
            Route::delete('/users/{userId}/groups/{groupId}', [AuthentikController::class, 'removeUserFromGroup'])->name('users.groups.remove');
            Route::get('/groups', [AuthentikController::class, 'groups'])->name('groups');
            Route::get('/groups/{groupId}/members', [AuthentikController::class, 'groupMembers'])->name('groups.members');
            Route::get('/applications', [AuthentikController::class, 'applications'])->name('applications');
        });
    });
}