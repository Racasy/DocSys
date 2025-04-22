<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DocumentRequest;
use Carbon\Carbon;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $adminName = Auth::user()->name;

        // Fetch statistics
        $stats = $this->getDashboardStats();

        return Inertia::render('Admin/Dashboard', [
            'adminName' => $adminName,
            'stats' => $stats
        ]);
    }

    private function getDashboardStats()
    {
        // Total requests
        $totalRequests = DocumentRequest::count();

        // In progress requests (assuming 'in_progress' is a status in document_requests)
        $inProgress = DocumentRequest::where('status', 'in_progress')->count();

        // Pending requests
        $pending = DocumentRequest::where('status', 'pending')->count();

        // Approved requests
        $approved = DocumentRequest::where('status', 'approved')->count();

        // Denied requests
        $denied = DocumentRequest::where('status', 'denied')->count();

        // Average processing time (in days)
        $avgProcessingTime = DocumentRequest::whereNotNull('submitted_at')
            ->selectRaw('AVG(DATEDIFF(submitted_at, created_at)) as avg_time')
            ->value('avg_time') ?? 0;

        // Active users (users with at least one request in the last 30 days)
        $activeUsers = User::whereHas('documentRequests', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subDays(30));
        })->count();

        // Overdue requests
        $overdue = DocumentRequest::where('status', '!=', 'approved')
            ->where('deadline', '<', Carbon::now())
            ->count();

        // Today's submissions
        $todaySubmissions = DocumentRequest::whereDate('submitted_at', Carbon::today())->count();

        return [
            'total' => $totalRequests,
            'in_progress' => $inProgress,
            'pending' => $pending,
            'approved' => $approved,
            'denied' => $denied,
            'avg_processing_time' => round($avgProcessingTime, 1),
            'active_users' => $activeUsers,
            'overdue' => $overdue,
            'today_submissions' => $todaySubmissions
        ];
    }
}