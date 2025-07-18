<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\DocumentRequest as UserRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $stats = [
            'total' => UserRequest::where('user_id', $userId)->count(),
            'pending' => UserRequest::where('user_id', $userId)->where('status', 'pending')->count(),
            'in_progress' => UserRequest::where('user_id', $userId)->where('status', 'in_progress')->count(),
            'approved' => UserRequest::where('user_id', $userId)->where('status', 'approved')->count(),
            'denied' => UserRequest::where('user_id', $userId)->where('status', 'denied')->count(),
        ];


        $latestRequest = UserRequest::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->first();

        return Inertia::render('User/Dashboard', [
            'latestRequest' => $latestRequest,
            'stats' => $stats
        ]);
    }

    public function show()
    {
        return Inertia::render('User/PrivPolicy', []);
    }
}
