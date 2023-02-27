<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function index(Request $request){
        return FileUpload::all();
    }

    public function file(Request $request, $id){
        $file = FileUpload::find($id);

        return response()->json($file, 200);
    }

    public function upload(Request $request){
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:jpg,jpeg,png,pdf|max:20480'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'request'=> $request->all(),
                'validFile'=> $request->file('file')->isValid(),
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $id = $request->user()->rocket_id;

        $fileUpload = new FileUpload;
        
        $file = $request->file('file');
        if (!$file) {
            $response = [
                'success' => false,
                'message' => 'File not found'
            ];

            return response()->json($response, 400);
        }

        $file_name = time().'_'.$id. '.' . $request->file('file')->getClientOriginalExtension();
        $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');

        $fileUpload->user = $id;
        $fileUpload->type = $request['type'];
        $fileUpload->name = $file_name;
        $fileUpload->path = '/storage/' . $file_path;

        if ($fileUpload->save()) {
            $response = [
                'success' => true,
                'id' => $fileUpload->id
            ];
        
            return response()->json($response, 200);
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to save file upload to database'
            ];
        
            return response()->json($response, 400);
        }
    }
}
