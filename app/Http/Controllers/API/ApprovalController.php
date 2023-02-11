<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Approval;

class ApprovalController extends Controller
{
    public function check(Request $request){
        $approval = Approval::find($request->user()->rocket_id);

        if($approval){
            $response = [
                'success'=> true,
                'message'=> 'Approval found.'
            ];

            return response()->json($response, 200);
        } else {
            $newApproval = new Approval();
            $newApproval->rocket_id = $request->user()->rocket_id;
            $newApproval->save();

            $response = [
                'success'=> true,
                'message'=> 'Approval created.'
            ];

            return response()->json($response, 200);
        }
    }

    public function user(Request $request){
        $validator = Validator::make($request->all(), [
            'rocket_id'=> 'required'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $approval = Approval::find($request->rocket_id);

        if($request->has('update')){
            $updates = $request->update;

            $approval->fill($updates);
            $approval->save();

            $response = [
                'success'=> true,
                'message'=> 'Approval updated.'
            ];

            return response()->json($response, 200);
        } elseif($request->has('valid')) {
            $checks = ['eo_file', 'eo_exp', 'eo_limit', 'license_file', 'license', 'agreement'];

            $valid = true;

            foreach ($checks as $item){
                if($approval[$item] == false){
                    $valid = false;
                }
            }

            if($valid){
                $approval->valid = $request->valid;
                $approval->save();

                $response = [
                    'success'=> true,
                    'message'=> 'User has been approved.'
                ];

                return response()->json($response, 200);
            } else {
                $response = [
                    'succes'=> false,
                    'message'=> 'Please validate all required fields.'
                ];

                return response()->json($response, 400);
            }
        } else {
            return response()->json($approval, 200);
        }
    }
}
