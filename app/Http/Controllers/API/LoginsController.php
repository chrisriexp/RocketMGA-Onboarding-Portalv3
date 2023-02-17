<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Logins;
use Dotenv\Parser\Value;

class LoginsController extends Controller
{
    public function check(Request $request){
        $logins = Logins::find($request->user()->rocket_id);

        if($logins){
            $response = [
                'success'=> true,
                'message'=> 'Logins found.'
            ];

            return response()->json($response, 200);
        } else {
            $newLogins = new Logins();
            $newLogins->rocket_id = $request->user()->rocket_id;
            $newLogins->save();

            $response = [
                'success'=> true,
                'message'=> 'Logins created.'
            ];

            return response()->json($response, 200);
        }
    }

    public function logins(Request $request){
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

        $logins = Logins::find($request->rocket_id);

        if($request->has('update')){
            $data = $request->update;

            $logins->fill($data);
            $logins->save();

            $response = [
                'succes'=> true,
                'message'=> 'Logins updated successfully.',
                'logins'=> $logins
            ];

            return response()->json($response, 200);
        } else {
            return response()->json(['success'=> true, 'logins'=> $logins], 200);
        }
    }
}
