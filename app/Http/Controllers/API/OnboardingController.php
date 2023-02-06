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
        $id = $request->user()->rocket_id;
        $data = $request['data'];

        if($request->has('step')){
            $step = $request['step'];

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
            } elseif($step == 2){
                $data['agency_tax_id'] = str_replace('-', '', $data['agency_tax_id']);

                $validator = Validator::make($data, [
                    'agent_license'=> 'required',
                    'agent_npn'=> 'required|digits:7',
                    'agent_license_eff'=> 'required',
                    'agent_license_exp'=> 'required',
                    'agency_license'=> 'required',
                    'agency_tax_id'=> 'required|digits:9',
                    'agency_type'=> 'required'
                ]);
            }
        } else {
            $validator = Validator::make($data, [

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

        if($request->has('step')){
            $step = $request['step'];
            
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
        }

        $response = [
            'success'=> true
        ];

        return response()->json($response, 200);
    }
}
