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
        $requestObj = DocumentRequest::where('id', $requestId)
            ->where('user_id', Auth::id())
            ->with('documents', 'comments.user') // Load comments with user info
            ->firstOrFail();

        $deadline = \Carbon\Carbon::parse($requestObj->deadline);
        $now = \Carbon\Carbon::now();
        $daysLeft = $now->diffInDays($deadline, false);

        return inertia('User/Requests/Show', [
            'documentRequest' => $requestObj,
            'daysLeft'        => $daysLeft,
        ]);
    }

    public function storeComment(Request $request, $requestId)
    {
        $docRequest = DocumentRequest::where('id', $requestId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        DocumentComment::create([
            'document_request_id' => $docRequest->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
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
        $failedFiles = [];
        $uploadedCount = 0;

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                try {
                    // Convert file to PDF
                    $pdfPath = $this->convertFileToPdf($file);

                    // Generate unique PDF name
                    $uniqueFileName = time() . '_' . uniqid() . '_' .
                        \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.pdf';
                    $storedPath = "documents/{$uniqueFileName}";

                    // Upload
                    $fileContents = file_get_contents($pdfPath);
                    \Log::info("Attempting to upload file to GCS: {$storedPath}, size: " . strlen($fileContents));

                    $uploadResult = Storage::disk('gcs')->put($storedPath, $fileContents);
                    if (!$uploadResult) {
                        \Log::error("GCS upload returned false for path: {$storedPath}");
                        throw new \Exception("GCS upload returned false");
                    }

                    // Clean up local PDF
                    if (file_exists($pdfPath)) {
                        unlink($pdfPath);
                    }

                    // Save doc record
                    $document = Document::create([
                        'user_id'      => $user->id,
                        'request_id'   => $docRequest->id,
                        'file_path'    => $storedPath,
                        'file_name'    => $uniqueFileName,
                        'file_size'    => strlen($fileContents),
                        'status'       => 'uploaded',
                        'uploaded_at'  => now(),
                    ]);

                    $uploadedFiles[] = $document->id;
                    $uploadedCount++;
                } catch (\Exception $e) {
                    \Log::error("Error uploading file {$file->getClientOriginalName()}: " . $e->getMessage());
                    // Instead of returning, collect the error
                    $failedFiles[] = "{$file->getClientOriginalName()} ({$e->getMessage()})";
                    // continue to next file
                    continue;
                }
            }
        }

        // Provide both success and error feedback
        if (!empty($failedFiles)) {
            return redirect()->back()->with([
                'success' => "Successfully uploaded {$uploadedCount} file(s).",
                'error'   => "Failed on: " . implode(', ', $failedFiles),
            ]);
        } else {
            return redirect()->back()->with('success', "Successfully uploaded {$uploadedCount} file(s).");
        }
    }

    public function submit($requestId)
    {
        $docRequest = DocumentRequest::where('id', $requestId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    
        if ($docRequest->status !== 'pending') {
            abort(403, 'Request is not pending or is already submitted.');
        }
    
        // Set status to in_progress
        $docRequest->status = 'in_progress';
        // Set the submitted date/time
        $docRequest->submitted_at = now();
        $docRequest->save();
    
        return redirect()->route('user.requests.show', $docRequest->id)
                         ->with('success', 'Request submitted successfully!');
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

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'tif', 'tiff', 'heic', 'jfif'])) {
            return $this->convertImageToPdf($file);
        } elseif (in_array($extension, ['txt', 'csv'])) {
            return $this->convertTextToPdf($file);
        } elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx'])) {
            return $this->convertOfficeToPdf($file);
        } elseif ($extension === 'pdf') {
            // Return the path as is
            return $file->getPathname();
        }
        
        throw new \Exception("Unsupported file type: {$extension}");
    }

    private function convertTextToPdf($file)
    {
        $content = file_get_contents($file->getPathname());
        $pdf = Pdf::loadHTML("<h3>Converted File</h3><p>" . nl2br(e($content)) . "</p>");
        $pdfPath = storage_path('app/temp/' . uniqid() . '.pdf');
        $pdf->save($pdfPath);
        return $pdfPath;
    }

    private function convertOfficeToPdf($file)
    {
        $originalPath = $file->getPathname();
        $tempDir = storage_path('app/temp');
        $uniqueName = uniqid();
        $pdfPath = $tempDir . '/' . $uniqueName . '.pdf';

        // We'll let LibreOffice produce the PDF with its default naming
        // Then rename it. Typically it will produce "filename.pdf" from "filename.doc" in $tempDir.
        $outputBase = pathinfo($originalPath, PATHINFO_FILENAME); // e.g. "file"
        $potentialOutput = $tempDir . '/' . $outputBase . '.pdf'; // e.g. /tmp/file.pdf

        // On Windows:
        $cmd = '"C:\\Program Files\\LibreOffice\\program\\soffice.exe" --headless --convert-to pdf --outdir ' 
             . escapeshellarg($tempDir) . ' ' 
             . escapeshellarg($originalPath);

        // (On Linux, you'd do something like)
        // $cmd = 'libreoffice --headless --convert-to pdf --outdir '
        //     . escapeshellarg($tempDir) . ' '
        //     . escapeshellarg($originalPath);

        shell_exec($cmd);

        // if the expected output is created
        if (file_exists($potentialOutput)) {
            rename($potentialOutput, $pdfPath);
        }

        // check if final $pdfPath exists
        if (!file_exists($pdfPath)) {
            throw new \Exception("LibreOffice failed to convert document to PDF.");
        }

        return $pdfPath;
    }

    private function convertImageToPdf($file)
    {
        // Move the uploaded image to a temp location
        $ext = strtolower($file->getClientOriginalExtension());
        $tempDir = storage_path('app/temp');
        $tempImagePath = $tempDir . '/' . uniqid() . '.' . $ext;
        $file->move($tempDir, basename($tempImagePath));

        // If the image type is something FPDF can't handle natively (HEIC, TIFF, JFIF),
        // convert it using Imagick to a standard format like JPG

        $convertible = ['heic','tif','tiff','jfif'];
        // if (in_array($ext, $convertible)) {
        //     $imagick = new \Imagick($tempImagePath);
        //     // If multi-page (TIFF), might need to flatten or handle pages in a loop
        //     $imagick->setImageFormat('jpg');
            
        //     $convertedPath = preg_replace('/\.' . $ext . '$/', '.jpg', $tempImagePath);
        //     $imagick->writeImage($convertedPath);
        //     // remove original
        //     unlink($tempImagePath);
        //     $tempImagePath = $convertedPath;
        // }

        if (in_array($ext, $convertible)) {
            try {
                $imagick = new \Imagick($tempImagePath);
                $imagick->setImageFormat('jpg');
                $convertedPath = preg_replace('/\.' . $ext . '$/', '.jpg', $tempImagePath);
                $imagick->writeImage($convertedPath);
                $imagick->clear();
                $imagick->destroy();
                unlink($tempImagePath);
                $tempImagePath = $convertedPath;
            } catch (\ImagickException $e) {
                \Log::error("Imagick failed reading HEIC: " . $e->getMessage());
                // Maybe re-throw or handle gracefully
                throw $e;
            }
        }

        // Now create a PDF from this image
        $pdfPath = $tempDir . '/' . uniqid() . '.pdf';

        $pdf = new Fpdi();
        $pdf->AddPage();

        list($width, $height) = getimagesize($tempImagePath);

        // Get page dimensions
        $pageWidth = $pdf->GetPageWidth();
        $pageHeight = $pdf->GetPageHeight();

        // Scale to fit entire page proportionally
        $ratioImage = $width / $height;
        $ratioPage = $pageWidth / $pageHeight;

        $fitWidth = $pageWidth;
        $fitHeight = $pageHeight;

        // if image is "wider" than page ratio
        if ($ratioImage > $ratioPage) {
            // match page width
            $fitHeight = $pageWidth / $ratioImage;
        } else {
            // match page height
            $fitWidth = $pageHeight * $ratioImage;
        }

        // Center the image
        $x = ($pageWidth - $fitWidth) / 2;
        $y = ($pageHeight - $fitHeight) / 2;

        $pdf->Image($tempImagePath, $x, $y, $fitWidth, $fitHeight);
        $pdf->Output($pdfPath, 'F');

        // clean up
        unlink($tempImagePath);

        return $pdfPath;
    }
    
    
    
}
