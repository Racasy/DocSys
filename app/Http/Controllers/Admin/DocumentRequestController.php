<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\User;

class DocumentRequestController extends Controller
{
    // Show only pending requests
    public function indexPending()
    {
        $requests = DocumentRequest::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at','desc')
            ->get();

        return inertia('Admin/Requests/Pending', [
            'requests' => $requests
        ]);
    }

    public function destroy($requestId)
    {
        $requestObj = DocumentRequest::findOrFail($requestId);

        // Delete all related documents (optional, remove if not needed)
        $requestObj->documents()->delete();

        // Delete the request
        $requestObj->delete();

        return redirect()->route('admin.requests.all')
            ->with('success', 'Request deleted successfully!');
    }

    // Show all requests, with optional filtering
    public function indexAll(Request $request)
    {
        $query = DocumentRequest::with('user');

        // Filter by status if provided
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Sort if provided
        if ($sort = $request->input('sort_by')) {
            $query->orderBy($sort, 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $requests = $query->get();

        return inertia('Admin/Requests/All', [
            'requests' => $requests
        ]);
    }

    // Create a new request for a user
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title'   => 'required|string',
            'description' => 'nullable|string',
            'deadline'    => 'nullable|date',
        ]);

        DocumentRequest::create([
            'user_id' => $validated['user_id'],
            'title'   => $validated['title'],
            'description' => $validated['description'] ?? '',
            'deadline'    => $validated['deadline'] ?? null,
            'status'      => 'pending',
        ]);

        return redirect()->route('admin.requests.pending')
            ->with('success','Request created successfully!');
    }

    public function create()
    {
    
        $users = User::where('role', 'user')->get(['id','name','email']);
    
        return inertia('Admin/Requests/Create', [
            'users' => $users
        ]);
    }

    // Show a specific request + its documents
    public function show($requestId)
    {
        $requestObj = DocumentRequest::with(['documents.comments.user','user'])
            ->findOrFail($requestId);

        return inertia('Admin/Requests/Show', [
            'documentRequest' => $requestObj
        ]);
    }

    // Approve the request
    public function approve($requestId)
    {
        $requestObj = DocumentRequest::findOrFail($requestId);
        $requestObj->status = 'approved';
        $requestObj->save();

        // Optionally notify user (e.g. create a Notification record)
        // ...

        return redirect()->route('admin.requests.all')
            ->with('success','Request approved!');
    }

    // Deny the request
    public function deny(Request $request, $requestId)
    {
        $requestObj = DocumentRequest::findOrFail($requestId);
        $requestObj->status = 'denied';
        $requestObj->save();
    
        // Extract base title (remove any existing suffix)
        preg_match('/^(.*?)(_([0-9]+))?$/', $requestObj->title, $matches);
        $baseTitle = $matches[1]; // Main title without suffix
        $suffixNumber = isset($matches[3]) ? intval($matches[3]) + 1 : 2; // Next suffix
    
        // Generate new title with the next available suffix
        $newTitle = "{$baseTitle}_{$suffixNumber}";
    
        // Create a new request with updated suffix
        DocumentRequest::create([
            'user_id'    => $requestObj->user_id,
            'title'      => $newTitle,
            'description'=> $requestObj->description,
            'deadline'   => $requestObj->deadline,
            'status'     => 'pending',
        ]);
    
        return redirect()->route('admin.requests.all')
            ->with('success', 'Request denied and a new request was created!');
    }    

}
