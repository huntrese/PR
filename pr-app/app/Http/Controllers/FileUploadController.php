<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function uploadJson(Request $request)
    {
        // Validate that the uploaded file is a JSON file
        $request->validate([
            'file' => 'required|file|mimetypes:application/json,text/plain',
        ]);

        // Retrieve the uploaded file
        $file = $request->file('file');

        // Store the file in the 'uploads' directory
        $path = $file->storeAs('uploads', 'uploaded_data.json');

        // Return a success message
        return response()->json([
            'message' => 'File uploaded successfully!',
            'path' => $path
        ], 200);
    }
}
