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
            $user->stage = 'agency';

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

                if($request['wright']){
                    $validator = Validator::make($data, [
                        'agent_license'=> 'required',
                        'agency_license'=> 'required',
                        'agency_tax_id'=> 'required|digits:9',
                        'agency_type'=> 'required'
                    ]);
                } else {
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
            } elseif($step == 3){
                $validator = Validator::make($data, [
                    'eo_exp'=> 'required',
                    'eo_policy'=> 'required',
                    'eo_limit'=> 'required',
                    'eo_insurer'=> 'required',
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
            
            if($step == 0){
                if($existingUser->stage == 'agency'){
                    $existingUser->stage = 'carrier';
                    $existingUser->save();
                }
            } elseif($step == 1){
                if($existingUser->stage == 'carrier'){
                    $existingUser->stage = 'entity';
                    $existingUser->save();
                }
                
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
            } elseif($step == 2 ){
                if($existingUser->stage == 'entity'){
                    $existingUser->stage = 'eo';
                    $existingUser->save();
                }
            } elseif($step == 3 ){
                if($existingUser->stage == 'eo'){
                    $existingUser->stage = 'agreement';
                    $existingUser->save();
                }
            }
        }

        $response = [
            'success'=> true
        ];

        return response()->json($response, 200);
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

        $user = onboardingInfo::find($request->rocket_id);

        if($request->has('appointed')){
            $user->appointed = $request->appointed;
            $user->save();

            $response = [
                'succes'=> true,
                'message'=> 'User appointed.'
            ];

            return response()->json($response, 200);
        } elseif($request->has('uip_created')){
            $user->uip_created = $request->uip_created;
            $user->save();

            $response = [
                'succes'=> true,
                'message'=> 'UIPs created..'
            ];

            return response()->json($response, 200);
        } elseif($request->has('approved')){
            $user->approved = $request->approved;
            $user->save();

            $response = [
                'succes'=> true,
                'message'=> 'User approved.'
            ];

            return response()->json($response, 200);
        } elseif($request->has('update')){
            $updates = $request->update;

            $updates['phone'] = preg_replace('/\D+/', '', $updates['phone']);
            $updates['agency_tax_id'] = str_replace('-', '', $updates['agency_tax_id']);

            $user->fill($updates);
            $user->save();

            $response = [
                'succes'=> true,
                'message'=> 'User info updated.'
            ];

            return response()->json($response, 200);
        } else {
            return response()->json($user, 200);
        }
    }

    public function index(Request $request, $filter = null){
        if($filter == null){
            return 'no filter';
        }
        //Dashboard Filter
        elseif($filter == 'dashboard'){
            $agents = onboardingInfo::all();
            $agentsCount = $agents->count();
            $agentsOnboarding = $agents->where('completed', false)->count();
            $agentsUnderReview = $agents->where('completed', true)->where('approved', false)->count();
            $agentsAppointed = $agents->where('appointed', true)->count();

            $data = onboardingInfo::orderBy('created_at', 'DESC')->take(4)->get(['rocket_id', 'agency_name', 'agent_name', 'phone', 'email', 'stage']);      

            $response = [
                'success'=> true,
                'agents'=> $data,
                'onboarding'=> $agentsOnboarding,
                'review'=> $agentsUnderReview,
                'appointed'=> $agentsAppointed,
                'totalCount'=> $agentsCount
            ];
            
            return response()->json($response, 200);
        }
        //Agents Onboarding Filter
        elseif($filter == 'agents'){
            $agents = onboardingInfo::where('completed', false)->get(['rocket_id', 'agency_name', 'agent_name', 'phone', 'email', 'stage']);     

            $response = [
                'success'=> true,
                'agents'=> $agents
            ];
            
            return response()->json($response, 200);
        }
        //Agents Under Review Filter
        elseif($filter == 'review'){
            $agents = onboardingInfo::where('completed', true)->where('approved', false)->get(['rocket_id', 'agency_name', 'agent_name', 'phone', 'email', 'stage']);     

            $response = [
                'success'=> true,
                'agents'=> $agents
            ];
            
            return response()->json($response, 200);
        }
        //Agents Approved Filter
        elseif($filter == 'approved'){
            $agents = onboardingInfo::where('completed', true)->where('approved', true)->get(['rocket_id', 'agency_name', 'agent_name', 'phone', 'email']);     

            $response = [
                'success'=> true,
                'agents'=> $agents
            ];
            
            return response()->json($response, 200);
        }
        //Agent Documents Filter
        elseif($filter == 'documents'){
            $agents = onboardingInfo::get(['rocket_id', 'agency_name', 'agent_license_file', 'agency_license_file', 'eo', 'agency_logo', 'document_id']);     

            $response = [
                'success'=> true,
                'agents'=> $agents
            ];
            
            return response()->json($response, 200);
        }
    }
}
