<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentRequest;
use App\Models\Document;
use App\Models\DocumentComment;
use Auth;

class DocumentRequestController extends Controller
{
    // Show all requests for the logged-in user
    public function index()
    {
        $requests = DocumentRequest::where('user_id', Auth::id())
            ->orderBy('created_at','desc')
            ->get();

        return inertia('User/Requests/Index', [
            'requests' => $requests
        ]);
    }

    // Show a specific request + existing documents
    public function show($requestId)
    {
        $requestObj = DocumentRequest::where('id',$requestId)
            ->where('user_id', Auth::id())
            ->with('documents.comments.user')
            ->firstOrFail();

        return inertia('User/Requests/Show', [
            'documentRequest' => $requestObj
        ]);
    }

    // Upload multiple documents + optional comment
    public function upload(Request $request, $requestId)
    {
        $docRequest = DocumentRequest::where('id',$requestId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'files' => 'required',
            'files.*' => 'file|max:51200', // e.g. up to 50MB
            'comment' => 'nullable|string'
        ]);

        $user = Auth::user();

        // For each uploaded file
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // 1) Convert to PDF + append page
                // We assume you have a helper method to do this
                $pdfPath = $this->convertFileToPdfWithDisclaimer($file);

                // 2) Generate new filename: {UserName}_{someIncrement}.pdf
                // If you track user->upload_count, increment that
                // or do something else to get a unique number
                $uniqueNumber = time(); // or a DB sequence
                $finalFileName = $user->name.'_'.$uniqueNumber.'.pdf';

                // 3) Store the final PDF
                $storedPath = Storage::putFileAs('documents', new \Illuminate\Http\File($pdfPath), $finalFileName);

                // 4) Create a documents entry
                $document = Document::create([
                    'user_id' => $user->id,
                    'request_id' => $docRequest->id,
                    'file_path' => $storedPath,
                    'file_name' => $finalFileName,
                    'file_size' => filesize($pdfPath),
                    'status' => 'uploaded',
                    'uploaded_at' => now(),
                ]);

                // 5) If there's a comment, store it
                if ($request->comment) {
                    DocumentComment::create([
                        'document_id' => $document->id,
                        'user_id' => $user->id,
                        'comment' => $request->comment,
                    ]);
                }

                // Remove temp $pdfPath if needed
            }
        }

        // If request was pending, you could set it to in_progress
        if ($docRequest->status === 'pending') {
            $docRequest->status = 'in_progress';
            $docRequest->save();
        }

        return redirect()->back()->with('success','Files uploaded successfully!');
    }

    /**
     * Convert the uploaded file to PDF and append a disclaimer page.
     * Implementation details will vary based on your PDF library or approach.
     */
    private function convertFileToPdfWithDisclaimer($file)
    {
        // 1) If $file is already PDF, just load it
        // 2) If not, convert doc/docx/jpg to PDF. Possibly use a library or
        //    an external service. We'll pseudo-code it here:

        // Pseudo-code using some imaginary PDF library:
        /*
        $tempPath = $file->getPathname(); // original
        $convertedPdf = SomePdfLibrary::convert($tempPath); 
        // -> returns a PDF resource

        // Append page
        $convertedPdf->addPage("THIS DOCUMENT HAS BEEN UPLOADED IN A SAFE WAY AND CAN BE HELD DIGITALLY");

        $outPath = storage_path('app/temp/'.uniqid().'.pdf');
        $convertedPdf->save($outPath);

        return $outPath;
        */

        // For demonstration, let's assume we just store the file as-is, 
        // ignoring actual conversion. In real usage, implement your conversion logic.
        $outPath = storage_path('app/temp/'.uniqid().'.pdf');
        file_put_contents($outPath, "PDF Content + Disclaimer (Fake) for demonstration.");

        return $outPath;
    }
}
