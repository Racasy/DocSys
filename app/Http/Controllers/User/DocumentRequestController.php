<?php

namespace App\Http\Controllers\User;

use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;
use setasign\Fpdi\PdfReader;

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

    public function upload(Request $request, $requestId)
    {
        $docRequest = DocumentRequest::where('id', $requestId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:51200', // Max 50MB per file
            'comment' => 'nullable|string',
        ]);
    
        $user = Auth::user();
        $uploadedFiles = [];
    
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Convert file to PDF if necessary
                $pdfPath = $this->convertFileToPdf($file);
    
                // Generate a unique filename
                $uniqueFileName = time() . '_' . uniqid() . '_' . pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.pdf';
    
                // Ensure directory exists
                $destinationPath = storage_path('app/public/documents');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
    
                // Move PDF to storage manually
                rename($pdfPath, $destinationPath . '/' . $uniqueFileName);
    
                // Store relative path
                $storedPath = "documents/{$uniqueFileName}";
    
                // Save document record in DB
                $document = Document::create([
                    'user_id' => $user->id,
                    'request_id' => $docRequest->id,
                    'file_path' => $storedPath,
                    'file_name' => $uniqueFileName,
                    'file_size' => filesize($destinationPath . '/' . $uniqueFileName),
                    'status' => 'uploaded',
                    'uploaded_at' => now(),
                ]);
    
                $uploadedFiles[] = $document->id;
            }
        }
    
        // If request was pending, mark it as in_progress
        if ($docRequest->status === 'pending') {
            $docRequest->status = 'in_progress';
            $docRequest->save();
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Files uploaded successfully!',
            'uploaded_files' => $uploadedFiles
        ]);
    }
    

    private function convertFileToPdf($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'tif', 'heic', 'svg'])) {
            return $this->convertImageToPdf($file);
        } elseif (in_array($extension, ['txt', 'csv'])) {
            return $this->convertTextToPdf($file);
        } elseif (in_array($extension, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'ods', 'odp'])) {
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
