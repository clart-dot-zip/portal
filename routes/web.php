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

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'portal.admin:false'])->name('dashboard');

Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'portal.admin:false'])->name('dashboard');

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
    
    // User Management Routes - Admin access required
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/onboard', [UserController::class, 'onboard'])->name('onboard');
        Route::post('/onboard', [UserController::class, 'processOnboard'])->name('onboard.process');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/sync', [UserController::class, 'sync'])->name('sync');
        Route::post('/{id}/toggle-admin', [UserController::class, 'togglePortalAdmin'])->name('toggle-admin');
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
        Route::delete('/{id}/access', [ApplicationController::class, 'removeAccess'])->name('remove-access');
    });
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Debug routes for development
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