<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

use App\Models\Document;
use App\Models\DocumentStamp;
use App\Models\DocumentRequest;
use App\Models\DocumentComment;

use App\Mail\DeniedRequestEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

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

    public function indexByYear(User $user, $year, Request $request)
    {
        $requests = DocumentRequest::where('user_id', $user->id)
            ->whereYear('created_at', $year);
    
        // Apply status filter
        if ($status = $request->input('status')) {
            $requests->where('status', $status);
        }
    
        // Apply sorting
        if ($sort = $request->input('sort_by')) {
            $requests->orderBy($sort, 'asc');
        } else {
            $requests->orderBy('created_at', 'desc');
        }
    
        return inertia('Admin/Requests/ByYear', [
            'requests' => $requests->get(),
            'user' => $user,
            'year' => $year
        ]);
    }

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

    public function download(Document $document)
    {
        try {
            // Generate a signed URL for temporary access (e.g., 30 minutes)
            $url = Storage::disk('gcs')->temporaryUrl($document->file_path, now()->addMinutes(30));
            return redirect($url);
        } catch (\Google\Cloud\Core\Exception\GoogleException $e) {
            \Log::error("Error generating signed URL: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate file URL: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error("Unexpected error generating signed URL: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate file URL: ' . $e->getMessage());
        }
    }

    public function stampAndReplace(Request $request, $id)
    {
        // Validate input - since we're receiving an array of stamps
        $request->validate([
            'stamps' => 'required|array|min:1|max:5',
            'stamps.*.debit' => 'required|string|max:255',
            'stamps.*.credit' => 'required|string|max:255',
            'stamps.*.amount' => 'required|numeric|min:0.01',
        ]);

        // Retrieve document with its stamps
        $document = Document::with('stamps')->findOrFail($id);
        $disk = Storage::disk('gcs');

        // The original file is located using the file_name (in folder "documents/")
        $originalPath = 'documents/' . $document->file_name;

        if (!$disk->exists($originalPath)) {
            return back()->withErrors(['file' => 'Fails nav atrasts mākonī']);
        }

        // Define the new stamped file path using the original file_name
        $stampedPath = 'documents/stamped_' . $document->file_name;
        
        // If a stamped version already exists, delete it.
        if ($disk->exists($stampedPath)) {
            $disk->delete($stampedPath);
        }

        // Download the original file to a temporary location
        $originalTemp = tempnam(sys_get_temp_dir(), 'orig_') . '.pdf';
        file_put_contents($originalTemp, $disk->get($originalPath));

        // Prepare a temporary file for the stamped PDF
        $stampedTemp = tempnam(sys_get_temp_dir(), 'stamp_') . '.pdf';

        // Stamp the PDF using FPDI
        $pdf = new \setasign\Fpdi\Fpdi();
        $pageCount = $pdf->setSourceFile($originalTemp);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tplId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);
        }

        // Append a new page with all stamp details
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Digitals zimogs', 0, 1);
        
        // Process each stamp in the request
        foreach ($request->stamps as $index => $stamp) {
            $pdf->Cell(0, 10, 'Zimogs #' . ($index + 1), 0, 1);
            $pdf->Cell(0, 10, 'D: ' . $stamp['debit'], 0, 1);
            $pdf->Cell(0, 10, 'K: ' . $stamp['credit'], 0, 1);
            $pdf->Cell(0, 10, 'Summa: ' . $stamp['amount'] . ' EUR', 0, 1);
            $pdf->Cell(0, 10, '------------------', 0, 1);
        }

        // Save the stamped PDF to the temporary file
        $pdf->Output($stampedTemp, 'F');

        // Upload the stamped file to GCS under the new path
        $disk->put($stampedPath, fopen($stampedTemp, 'r+'));

        // If we're updating all stamps, delete the existing ones
        if ($request->has('edit_mode') && $request->edit_mode) {
            $document->stamps()->delete();
        }

        // Save all stamps to the database
        $stampStartIndex = $document->stamps()->count();
        foreach ($request->stamps as $index => $stamp) {
            DocumentStamp::create([
                'document_id' => $document->id,
                'debit_account' => $stamp['debit'],
                'credit_account' => $stamp['credit'],
                'amount' => $stamp['amount'],
                'stamp_index' => $stampStartIndex + $index,
                'stamped_by' => auth()->id(),
                'stamped_at' => now(),
            ]);
        }

        // Update the document record to now point to the stamped file
        $document->file_path = $stampedPath;
        $document->save();

        // Remove temporary files
        unlink($originalTemp);
        unlink($stampedTemp);

        return back()->with('success', 'Fails veiksmīgi apzīmogots un aizvietots mākonī.');
    }

    public function editStamps(Request $request, $id)
    {
        // Validate input - similar to stampAndReplace
        $request->validate([
            'stamps' => 'required|array|min:1|max:5',
            'stamps.*.debit' => 'required|string|max:255',
            'stamps.*.credit' => 'required|string|max:255',
            'stamps.*.amount' => 'required|numeric|min:0.01',
        ]);

        // Get the document
        $document = Document::findOrFail($id);
        
        // Delete existing stamps
        $document->stamps()->delete();
        
        // Create new stamps
        foreach ($request->stamps as $index => $stamp) {
            DocumentStamp::create([
                'document_id' => $document->id,
                'debit_account' => $stamp['debit'],
                'credit_account' => $stamp['credit'],
                'amount' => $stamp['amount'],
                'stamp_index' => $index,
                'stamped_by' => auth()->id(),
                'stamped_at' => now(),
            ]);
        }
        
        // Re-stamp the document with updated stamps
        return $this->stampAndReplace($request, $id);
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

        return redirect()->route('admin.requests.create')
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
        $requestObj = DocumentRequest::with([
            'documents.stamps',
            'user',
            'comments.user'
        ])->findOrFail($requestId);

        return inertia('Admin/Requests/Show', [
            'documentRequest' => $requestObj
        ]);
    }

    public function storeComment(Request $request, $requestId)
    {
        $docRequest = DocumentRequest::findOrFail($requestId);

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        DocumentComment::create([
            'document_request_id' => $docRequest->id,
            'user_id' => auth()->id(), // Admin's user ID
            'comment' => $request->comment,
        ]);

        return redirect()->back();
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
        $request->validate(['reason' => 'required|string']);
    
        try {
            DB::beginTransaction();
    
            $requestObj = DocumentRequest::findOrFail($requestId);
            $requestObj->status = 'denied';
            $requestObj->save();
    
            // Send email
            Mail::to($requestObj->user->email)->send(
                new DeniedRequestEmail(
                    $requestObj->user,
                    $requestObj->title,
                    $request->reason
                )
            );
    
            // Create new request (optional)
            $newTitle = $this->generateNewTitle($requestObj->title);
            DocumentRequest::create([
                'user_id' => $requestObj->user_id,
                'title' => $newTitle,
                'description' => $requestObj->description,
                'deadline' => $requestObj->deadline,
                'status' => 'pending',
            ]);
    
            DB::commit();
    
            return redirect()->route('admin.users.index')
                ->with('success', 'Request denied and new one created!');
    
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Deny Request Failed: " . $e->getMessage());
            return back()->withErrors(['error' => 'Kļūda noraidot pieprasījumu!']);
        }
    }
    
    private function generateNewTitle($originalTitle)
    {
        if (preg_match('/^(.*?)(_(\d+))?$/', $originalTitle, $matches)) {
            $base = $matches[1];
            $suffix = isset($matches[3]) ? (int)$matches[3] + 1 : 2;
            return "{$base}_{$suffix}";
        }
        return "{$originalTitle}_2";
    }
}