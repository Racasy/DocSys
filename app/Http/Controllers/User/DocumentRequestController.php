<?php

namespace App\Http\Controllers\User;

use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;
use setasign\Fpdi\PdfReader;
use Google\Cloud\Storage\StorageClient;

use Illuminate\Http\File;
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

    public function destroy($id)
    {
        $document = Document::where('id', $id)
            ->whereHas('documentRequest', fn ($q) => $q->where('user_id', auth()->id()))
            ->firstOrFail();

        if ($document->documentRequest->status !== 'pending') {
            abort(403, 'Cannot delete a document unless request is pending.');
        }
    
        // Delete from GCS
        Storage::disk('gcs')->delete($document->file_path);
    
        // Delete from DB
        $document->delete();
    
        return redirect()->back()->with('message', 'Dokuments veiksmīgi izdzēsts.');
    }

    public function upload(Request $request, $requestId)
    {
        $docRequest = DocumentRequest::where('id', $requestId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($docRequest->status !== 'pending') {
            abort(403, 'Cannot upload to a request that is not pending.');
        }

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:51200', // Max 50MB per file
            'comment' => 'nullable|string',
        ]);

        $user = Auth::user();
        $uploadedFiles = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                try {
                    // Convert file to PDF if necessary
                    $pdfPath = $this->convertFileToPdf($file);

                    // Generate a unique filename
                    $uniqueFileName = time() . '_' . uniqid() . '_' . \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
                    $storedPath = "documents/{$uniqueFileName}"; // Path in GCS bucket

                    // Upload the PDF to GCS using Storage facade
                    $fileContents = file_get_contents($pdfPath);
                    \Log::info("Attempting to upload file to GCS: {$storedPath}, size: " . strlen($fileContents) . " bytes");

                    $uploadResult = Storage::disk('gcs')->put($storedPath, $fileContents);
                    if (!$uploadResult) {
                        \Log::error("GCS upload returned false for path: {$storedPath}");
                        throw new \Exception("GCS upload returned false");
                    }

                    \Log::info("Successfully uploaded file to GCS: {$storedPath}");

                    // Clean up temporary local PDF file
                    if (file_exists($pdfPath)) {
                        unlink($pdfPath);
                    }

                    // Get file size
                    $fileSize = strlen($fileContents);

                    // Save document record in DB
                    $document = Document::create([
                        'user_id' => $user->id,
                        'request_id' => $docRequest->id,
                        'file_path' => $storedPath,
                        'file_name' => $uniqueFileName,
                        'file_size' => $fileSize,
                        'status' => 'uploaded',
                        'uploaded_at' => now(),
                    ]);

                    $uploadedFiles[] = $document->id;
                } catch (\Google\Cloud\Core\Exception\GoogleException $e) {
                    \Log::error("GCS client error: " . $e->getMessage());
                    return redirect()->back()->with('error', 'Failed to upload file: ' . $e->getMessage());
                } catch (\Exception $e) {
                    \Log::error("Unexpected error during GCS upload: " . $e->getMessage());
                    return redirect()->back()->with('error', 'Failed to upload file: ' . $e->getMessage());
                }
            }
        }

        // If request was pending, mark it as in_progress
        if ($docRequest->status === 'pending') {
            $docRequest->status = 'in_progress';
            $docRequest->save();
        }

        return redirect()->back()->with('success', 'Files uploaded successfully!');
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


    private function convertFileToPdf($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'tif', 'heic', 'svg'])) {
            return $this->convertImageToPdf($file);
        } elseif (in_array($extension, ['txt', 'csv'])) {
            return $this->convertTextToPdf($file);
        } elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx'])) {
            return $this->convertOfficeToPdf($file);
        } elseif ($extension === 'pdf') {
            return $file->getPathname();
        }

        throw new \Exception("Unsupported file type: {$extension}");
    }

    private function convertTextToPdf($file)
    {
        $content = file_get_contents($file->getPathname());
    
        $pdf = Pdf::loadHTML("<h1>Converted File</h1><p>{$content}</p>");
        $pdfPath = storage_path('app/temp/' . uniqid() . '.pdf');
        $pdf->save($pdfPath);
    
        return $pdfPath;
    }

    private function convertOfficeToPdf($file)
    {
        $originalPath = $file->getPathname();
        $pdfPath = storage_path('app/temp/' . uniqid() . '.pdf');
    
        // Run LibreOffice conversion
        // if linux
        //shell_exec("libreoffice --headless --convert-to pdf --outdir " . escapeshellarg(dirname($pdfPath)) . " " . escapeshellarg($originalPath));
        
        // if windows
        shell_exec('"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf --outdir ' . escapeshellarg(dirname($pdfPath)) . ' ' . escapeshellarg($originalPath));

        return file_exists($pdfPath) ? $pdfPath : $originalPath;
    }

    private function convertImageToPdf($file)
    {
        // Move uploaded image to a temporary location
        $tempImagePath = storage_path('app/temp/' . uniqid() . '.' . $file->getClientOriginalExtension());
        $file->move(dirname($tempImagePath), basename($tempImagePath)); // Move file
    
        // Create a new PDF
        $pdfPath = storage_path('app/temp/' . uniqid() . '.pdf');
        $pdf = new Fpdi();
        $pdf->AddPage();
    
        // Get image size
        list($width, $height) = getimagesize($tempImagePath);
    
        // Convert pixel dimensions to points (1px ≈ 0.75 points)
        $pdfWidth = $width * 0.75;
        $pdfHeight = $height * 0.75;
    
        // Set PDF size based on image dimensions
        $pdf->SetAutoPageBreak(false);
        $pdf->Image($tempImagePath, 10, 10, $pdfWidth / 3, $pdfHeight / 3); // Resize to fit
    
        $pdf->Output($pdfPath, 'F'); // Save PDF
    
        // Delete temp image file
        unlink($tempImagePath);
    
        return $pdfPath;
    }
    
    
    
}
