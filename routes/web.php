<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;

use App\Http\Controllers\Admin\DocumentRequestController as AdminRequestController;
use App\Http\Controllers\User\DocumentRequestController as UserRequestController;


use Illuminate\Foundation\Application;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Routes with auth
Route::middleware('auth')->group(function () {

    // Routes for Admin
    Route::middleware([RoleMiddleware::class.':admin'])->group(function () { 

        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('adminDashboard');

        Route::get('/admin/requests/pending', [AdminRequestController::class, 'indexPending'])
            ->name('admin.requests.pending');

        Route::get('/admin/requests', [AdminRequestController::class, 'indexAll'])
            ->name('admin.requests.all');

        Route::post('/admin/requests', [AdminRequestController::class, 'store'])
            ->name('admin.requests.store');

        Route::get('/admin/requests/{requestId}', [AdminRequestController::class, 'show'])
            ->name('admin.requests.show');

        Route::get('/admin/requests/create', [AdminRequestController::class, 'create'])
            ->name('admin.requests.create');

        // Approve or deny
        Route::post('/admin/requests/{requestId}/approve', [AdminRequestController::class, 'approve'])
            ->name('admin.requests.approve');

        Route::post('/admin/requests/{requestId}/deny', [AdminRequestController::class, 'deny'])
            ->name('admin.requests.deny');
        });

    // Routes for User
    Route::middleware([RoleMiddleware::class.':user'])->group(function () { 
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
        Route::get('/user/requests', [UserRequestController::class, 'index'])
        ->name('user.requests.index');

        Route::get('/user/requests/{requestId}', [UserRequestController::class, 'show'])
            ->name('user.requests.show');

        Route::post('/user/requests/{requestId}/upload', [UserRequestController::class, 'upload'])
            ->name('user.requests.upload');
        
    
    });
});

require __DIR__.'/auth.php';