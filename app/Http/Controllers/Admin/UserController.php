<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\Document;
use App\Models\DocumentComment;
use App\Models\DocumentStamp;
use Illuminate\Support\Facades\Storage;

use Inertia\Inertia;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\WelcomeEmail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Get query parameters
        $searchQuery = $request->input('searchQuery', '');
        $filterOption = $request->input('filterOption', 'all');
        $sortOption = $request->input('sortOption', 'name');

        // Start building the query
        $query = User::where('role', 'user')
            ->select('id', 'name')
            ->withCount('documentRequests');

        // Apply search filter
        if ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }

        // Apply category filter
        if ($filterOption !== 'all') {
            if ($filterOption === 'hasRequests') {
                $query->has('documentRequests'); // Users with at least one document request
            } elseif ($filterOption === 'noRequests') {
                $query->doesntHave('documentRequests'); // Users with no document requests
            }
        }

        // Apply sorting
        if ($sortOption === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sortOption === 'name-desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sortOption === 'requests') {
            $query->orderBy('document_requests_count', 'asc');
        } elseif ($sortOption === 'requests-desc') {
            $query->orderBy('document_requests_count', 'desc');
        }

        // Paginate the results
        $users = $query->paginate(20);

        return Inertia::render('Admin/Requests/Users', [
            'users' => $users,
            'filters' => [
                'searchQuery' => $searchQuery,
                'filterOption' => $filterOption,
                'sortOption' => $sortOption,
            ],
        ]);
    }

    public function years(User $user)
    {
        // Select only the necessary fields for the user
        $user->loadCount('documentRequests'); // Load the document requests count
        $userData = $user->only('id', 'name', 'document_requests_count'); // Limit the fields

        // Fetch distinct years for the user's document requests
        $years = DocumentRequest::where('user_id', $user->id)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return Inertia::render('Admin/Requests/Years', [
            'user' => $userData,
            'years' => $years,
        ]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
            ]);

            $temporaryPassword = Str::random(12);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($temporaryPassword),
                'role' => 'user',
            ]);

            // Send welcome email using the Mailable
            \Mail::to($user->email)->send(new WelcomeEmail($user, $temporaryPassword));

            return redirect()->route('admin.users.index')
                ->with('success', 'Uzņēmums veiksmīgi izveidots, un sveiciena e-pasts nosūtīts.');
        }

        return Inertia::render('Admin/Requests/CreateUser');
    }

    public function users(Request $request)
    {
        // Get query parameters
        $searchQuery = $request->input('searchQuery', '');
        $sortOption = $request->input('sortOption', 'name');

        // Start building the query
        $query = User::where('role', 'user')
            ->select('id', 'name', 'is_disabled');

        // Apply search filter
        if ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%');
        }


        // Apply sorting
        if ($sortOption === 'name') {
            $query->orderBy('name', 'asc');
        } elseif ($sortOption === 'name-desc') {
            $query->orderBy('name', 'desc');
        }
        // Paginate the results
        $users = $query->paginate(20);

        return Inertia::render('Admin/ManageUsers', [
            'users' => $users,
            'filters' => [
                'searchQuery' => $searchQuery,
                'sortOption' => $sortOption,
            ],
        ]);
    }

    public function user($id)
    {
        $user = User::findOrFail($id);
        return Inertia::render('Admin/UserManagement', ['user' => $user]);
    }

    public function toggleDisable($id)
    {
        $user = User::findOrFail($id);
        $user->is_disabled = !$user->is_disabled;
        $user->save();
        return back()->with('success', $user->is_disabled ? 'User disabled' : 'User enabled');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Get all documents associated with the user
        $documents = Document::where('user_id', $id)->get();

        // Delete files from Google Cloud Storage
        foreach ($documents as $document) {
            Storage::disk('gcs')->delete($document->file_path);
        }

        // Delete the user (this will cascade delete all related data via database constraints)
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted');
    }
}