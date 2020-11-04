<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth:sanctum',
            'role:Admin'
        ])->except('streamAsPublic');
    }

    /**
     * Stream a public file as a response.
     * 
     * @param \Illuminate\Http\Request $request,
     * @param string $encryptedID
     * @return \Illuminate\Http\Response
     */
    public function streamAsPublic(Request $request, $encryptedID)
    {
        $id = Crypt::decryptString($encryptedID);
        $file = File::findOrFail($id);
        if (!$file->public) {
            return new Response('', 404);
        }
        return new Response(Storage::get($file->url), 200, [
            'Content-Type' => $file->type,
            'Content-Length' => $file->size,
        ]);
    }

    /**
     * Stream a private file as a response.
     * 
     * @param \Illuminate\Http\Request $request,
     * @param string $encryptedID
     * @return \Illuminate\Http\Response
     */
    public function streamAsPrivate(Request $request, $encryptedID)
    {
        $id = Crypt::decryptString($encryptedID);
        $file = File::findOrFail($id);
        return new Response(Storage::get($file->url), 200, [
            'Content-Type' => $file->type,
            'Content-Length' => $file->size,
        ]);
    }
}
