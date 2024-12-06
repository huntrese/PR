<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{
    // Write operation: only proceed if no read operations are happening
    public function writeToFile(Request $request)
    {
        // Perform write operation to the shared file
        $content = $request->input('content');
        Storage::disk('local')->put('shared_file.txt', $content);

        return response()->json(['message' => 'Write operation successful']);
    }

    // Read operation: can only happen after all writes are complete
    public function readFromFile()
    {
        $content = Storage::disk('local')->get('shared_file.txt');
        
        return response()->json(['content' => $content]);
    }
}
