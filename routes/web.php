<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AuditLogController;

use App\Http\Middleware\RoleMiddleware;

use App\Http\Controllers\Admin\DocumentRequestController as AdminRequestController;
use App\Http\Controllers\User\DocumentRequestController as UserRequestController;


use App\Http\Controllers\Admin\UserController;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

use App\Mail\TestMail;

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
        
        Route::get('/admin/documents/{filename}', function ($filename) {
            $path = storage_path("app\\public\\documents\\{$filename}"); 
            
            if (!file_exists($path)) {
                \Log::error("File not found: " . $path);
                abort(404, 'File not found.');
            }
        
            return Response::file($path);
        })->name('admin.documents.view');

        Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])
        ->name('admin.audit.logs');

        Route::get('/admin/users', [UserController::class, 'index'])
            ->name('admin.users.index');
        Route::get('/admin/users/{user}/years', [UserController::class, 'years'])
            ->name('admin.users.years');
        Route::get('/admin/users/{user}/years/{year}/requests', [AdminRequestController::class, 'indexByYear'])
            ->name('admin.users.years.requests');

        Route::get('/admin/users/create', [UserController::class, 'create'])
            ->name('admin.users.create');
        Route::post('/admin/users/create', [UserController::class, 'create'])
            ->name('admin.users.store');

        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('adminDashboard');

        Route::get('/admin/requests/pending', [AdminRequestController::class, 'indexPending'])
            ->name('admin.requests.pending');

        Route::get('/admin/requests', [AdminRequestController::class, 'indexAll'])
            ->name('admin.requests.all');

        Route::post('/admin/requests', [AdminRequestController::class, 'store'])
            ->name('admin.requests.store');
            
        Route::post('/admin/requests/{request}/comment', [AdminRequestController::class, 'storeComment'])
            ->name('admin.requests.comment');

        Route::get('/admin/requests/create', [AdminRequestController::class, 'create'])
            ->name('admin.requests.create');

        Route::post('/admin/documents/{id}/stamp', [AdminRequestController::class, 'stampAndReplace'])
            ->name('admin.documents.stamp');
        
        Route::post('/admin/documents/{id}/edit-stamps', [AdminRequestController::class, 'editStamps'])
            ->name('admin.documents.edit-stamps');

        Route::get('/admin/requests/{requestId}', [AdminRequestController::class, 'show'])
            ->name('admin.requests.show');
        
        Route::delete('/admin/requests/{id}', [AdminRequestController::class, 'destroy'])
            ->name('admin.requests.destroy');

        Route::get('/admin/documents/{document}/download', [AdminRequestController::class, 'download'])
            ->name('admin.documents.download');


        Route::get('/admin/manage-users', [UserController::class, 'users'])
            ->name('admin.users');

        Route::get('/admin/user/{id}', [UserController::class, 'user'])
        ->name('admin.user');

        Route::put('/admin/user/{id}/toggle-disable', [UserController::class, 'toggleDisable'])
        ->name('admin.user.toggleDisable');

        Route::delete('/admin/user/{id}', [UserController::class, 'deleteUser'])
        ->name('admin.user.delete');
        

        // Approve or deny
        Route::post('/admin/requests/{requestId}/approve', [AdminRequestController::class, 'approve'])
            ->name('admin.requests.approve');

        Route::post('/admin/requests/{requestId}/deny', [AdminRequestController::class, 'deny'])
            ->name('admin.requests.deny');
        });

    // Routes for User
    Route::middleware([RoleMiddleware::class.':user'])->group(function () { 

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/policy', [DashboardController::class, 'show'])->name('show');

        Route::get('/user/documents/{document}/download', [UserRequestController::class, 'download'])
            ->name('documents.download');

        Route::delete('/user/documents/{document}', [UserRequestController::class, 'destroy'])
            ->name('user.documents.destroy');


        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
        Route::get('/user/requests', [UserRequestController::class, 'index'])
        ->name('user.requests.index');

        Route::get('/user/requests/{requestId}', [UserRequestController::class, 'show'])
            ->name('user.requests.show');

        Route::post('/user/requests/{requestId}/submit', [UserRequestController::class, 'submit'])
            ->name('user.requests.submit');

        Route::post('/user/requests/{requestId}/upload', [UserRequestController::class, 'upload'])
            ->name('user.requests.upload');

        Route::post('/requests/{requestId}/comment', [UserRequestController::class, 'storeComment'])
            ->name('user.requests.comment');


    });
});

require __DIR__.'/auth.php';