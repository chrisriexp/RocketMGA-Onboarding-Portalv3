<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\onboardingInfo;
use App\Models\FileUpload;

class DownloadController extends Controller
{
    public function link(Request $request){
        $client = new \GuzzleHttp\Client();

        // Validate Request
        $validator = Validator::make($request->all(), [
            'rocket_id'=> 'required',
            'type'=>  'required',
            'link'=> 'required'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        // Rocket ID
        $id = $request['rocket_id'];
        // File Type
        $type = $request['type'];
        // File Name
        $file_name = time().'_'.$request['type'].'_'.$request['rocket_id']. '.pdf';

        // Download Document from Link
        $documentFile = $client->request('GET', $request['link'], [
            'sink' => storage_path('/app/public/uploads/'.$file_name)
        ]);

        // Save Document to file_uploads Database
        $fileUpload = new FileUpload;
        $fileUpload->user = $request['rocket_id'];
        $fileUpload->type = $request['type'];
        $fileUpload->name = $file_name;
        $fileUpload->path = '/storage/uploads/'.$file_name;
        $fileUpload->save();

        // Update User Onboarding Info
        $user = onboardingInfo::find($id);

        $user->$type = $fileUpload->id;
        $user->save();
        
        // Response
        $response = [
            'success'=> true,
            'message'=> "Document download successful.",
            'link'=> url('/storage/uploads/'.$file_name)
        ];

        return response()->json($response, 200);
    }
}
