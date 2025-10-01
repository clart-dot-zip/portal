<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthentikController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

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
