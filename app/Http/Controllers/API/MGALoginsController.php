<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MGALogins;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class MGALoginsController extends Controller
{
    public function logins($rocket_id){
        $logins = MGALogins::where('rocket_id', $rocket_id)->get();

        $response = [
            'success'=> true,
            'logins'=> $logins
        ];

        return response()->json($response, 200);
    }

    public function add(Request $request){
        // Validate Request
        $validator = Validator::make($request->all(), [
            'rocket_id'=> 'required',
            'name'=> 'required',
            'email'=> 'required|email'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $login = new MGALogins();
        $login->fill($request->all());
        $login->role = 'agent';
        $login->password = Str::random(9);
        $login->save();

        $response = [
            'success'=> true,
            'message'=> 'New MGA login created.'
        ];

        return response()->json($response, 200);
    }

    public function drop($id){
        MGALogins::destroy($id);

        $response = [
            'success'=> true,
            'message'=> 'Login deleted successfully.'
        ];

        return response()->json($response, 200);
    }
}
