<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\FileUpload;

class FileController extends Controller
{
    public function upload(Request $request){
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $id = $request->user()->rocket_id;

        $fileUpload = new FileUpload;

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