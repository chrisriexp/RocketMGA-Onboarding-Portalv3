<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\onboardingInfo;

class OnboardingController extends Controller
{
    public function check(Request $request){
        $id = $request->user()->rocket_id;
        $email = $request->user()->email;
        $name = $request->user()->name;

        $existingUser = onboardingInfo::find($id);

        if($existingUser){
            $response = [
                'success'=> true,
                'message'=> $existingUser
            ];

            return response()->json($response, 200);
        } else {
            $user = new onboardingInfo;
            $user->rocket_id = $id;
            $user->email = $email;
            $user->agent_name = $name;

            $user->save();

            $response = [
                'success'=> true,
                'message'=> $existingUser
            ];

            return response()->json($response, 200);
        }
    }

    public function update(Request $request){
        $data = $request['data'];
        $step = $request['step'];

        $id = $request->user()->rocket_id;

        if($step == 0){
            $data['phone'] = preg_replace('/\D+/', '', $data['phone']);

            $validator = Validator::make($data, [
                'agency_name'=> 'required',
                'agent_name'=> 'required',
                'email'=> 'required|email',
                'phone'=> 'required|digits:10',
                'address'=> 'required'
            ]);
        } elseif($step == 1){
            $validator = Validator::make($data, [
                'carriers'=> 'required',
                'additional_states'=> 'required'
            ]);
        }

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $existingUser = onboardingInfo::find($id);

        // foreach ($data as $key => $value){
        //     $existingUser[$key] = $value;
        //     $existingUser->save();
        // }

        $existingUser->fill($data);
        $existingUser->save();

        if($step == 1){
            $carriers = json_decode($data['carriers'], true);
            $mgaCarriers = ['aon', 'beyond', 'dual', 'flow', 'neptune', 'palomar', 'sterling', 'wright'];

            $results = [];
            foreach ($mgaCarriers as $carrier){
                $found = false;
                foreach ($carriers as $value){
                    if(str_contains(strtolower($value['name']), $carrier)){
                        $found = true;
                        break;
                    }
                }
                $results[$carrier] = $found;
            }

            $existingUser->fill($results);
            $existingUser->save();
        }

        $response = [
            'success'=> true
        ];

        return response()->json($response, 200);
    }
}
