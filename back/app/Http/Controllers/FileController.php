<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use Illuminate\Http\Request;

class FileController extends Controller{
    public function index(Request $request){return File::where('user_id', $request->user()->id)->with('user')->get();}
    public function store(StoreFileRequest $request){return File::create($request->all());}
    public function show(File $file){return $file;}
    public function update(UpdateFileRequest $request, File $file){return $file->update($request->all());}
    public function destroy(File $file){return $file->delete();}
}
