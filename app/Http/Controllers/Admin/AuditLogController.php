<?php

   namespace App\Http\Controllers\Admin;

   use App\Http\Controllers\Controller;
   use App\Models\AuditLog;
   use Illuminate\Http\Request;
   use Inertia\Inertia;

   class AuditLogController extends Controller
   {
        public function index(Request $request)
        {
            $query = AuditLog::with('user')->orderBy('created_at', 'desc');

            // Search by user name
            if ($request->has('name') && $request->name !== '') {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->name . '%');
                });
            }

            if ($request->has('actions') && count($request->actions) > 0) {
                $query->whereIn('action', $request->actions);
            }

            $logs = $query->paginate(20);

            return Inertia::render('Admin/AuditLogs', [
                'logs' => $logs,
                'filters' => $request->only(['name', 'actions']),
            ]);
        }
   }