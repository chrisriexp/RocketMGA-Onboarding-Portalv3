<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function validateToken(Request $request){
        $id = $request->user()->id;

        $tokenExists = DB::table('personal_access_tokens')->where('tokenable_id', $id)->exists();

        $premitedRoles = ['admin', 'super-admin', 'marketing'];

        if($request->has('admin')){
            if(in_array($request->user()->role, $premitedRoles)){
                if($tokenExists){
                    $response = [
                        'valid'=> true,
                        'role'=> $request->user()->role,
                        'message'=> 'Token authenticated successfully.'
                    ];
        
                    return response()->json($response, 200);
                } else {
                    $response = [
                        'valid'=> false,
                        'message'=> 'Token authentication failed.'
                    ];
        
                    return response()->json($response, 401);
                }
            } else {
                $response = [
                    'valid'=> false,
                    'message'=> 'User is not an admin account.'
                ];

                return response()->json($response, 401);
            }
        } else {
            if($tokenExists){
                $response = [
                    'valid'=> true,
                    'message'=> 'Token authenticated successfully.'
                ];
    
                return response()->json($response, 200);
            } else {
                $response = [
                    'valid'=> false,
                    'message'=> 'Token authentication failed.'
                ];
    
                return response()->json($response, 401);
            }
        }
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'password'=> 'required|min:8'
        ]);

        if($validator->fails()) {
            $response = [
                'success'=>false,
                'message'=>$validator->errors()
            ];

            return response()->json($response, 400);
        }

        if(Auth::attempt(['email'=> strtolower($request->email), 'password'=> $request->password])){
            $user = Auth::user();
            
            $token = $user->createToken('token', [$user->role])->plainTextToken;

            $response = [
                'success'=> true,
                'role'=> $user->role,
                'token'=> $token,
                'message'=> 'User login successful.'
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success'=> false,
                'message'=> 'Incorrect username or password.'
            ];

            return response()->json($response, 401);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'email'=> 'required|email',
            'password'=> 'required|min:8',
            'confirm_password'=> 'required|same:password'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $findUser = User::where('email', strtolower($request->email))->exists();

        if($findUser){
            $response = [
                'success'=> false,
                'message'=> 'User already exists.'
            ];

            return response()->json($response, 401);
        }

        $input = $request->all();
        $input['email'] = strtolower($input['email']);
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);
        $count = 21000 + $user->id;
        $user->rocket_id = "RMA{$count}";
        $user->save();

        $response = [
            'success'=> true,
            'message'=> 'User registered successfully.'
        ];

        return response()->json($response, 200);
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'password'=> 'required|min:8',
            'confirm_password'=> 'required|same:password'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false, 
                'message'=> $validator->errors()
            ];
            
            return response()->json($response, 400);
        }

        $user = User::where('email', $request->email)->first();

        if($user){
            $user->password = bcrypt($request->password);
            $user->save();

            $response = [
                'success'=> true,
                'message'=> 'Password reset successfully.'
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success'=> false,
                'message'=> 'User does not exist.'
            ];

            return response()->json($response, 401);
        }
    }

    public function resetEmail(Request $request){
        $validator = Validator::make($request->all(), [
            'email'=> 'required|email',
            'confirm_email'=> 'required|same:email',
            'password'=> 'required|min:8',
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false, 
                'message'=> $validator->errors()
            ];
            
            return response()->json($response, 400);
        }

        $user = User::where('email', $request->user()->email)->first();

        if(Auth::attempt(['email'=> $request->user()->email, 'password'=> $request->password])){
            $user->email = strtolower($request->email);
            $user->save();

            $response = [
                'success'=> true,
                'message'=> 'Email reset successfully.'
            ];

            return response()->json($response, 200);
        } else {
            $response = [
                'success'=> false,
                'message'=> 'Password is incorrect.'
            ];

            return response()->json($response, 401);
        }
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json(['success'=> true, 'message'=> 'User logged out successfully.'], 200);
    }

    public function users(){
        $users = User::where('role', '!=', 'user')->where('role', '!=', 'super-admin')->get(['id', 'name', 'email', 'rocket_id', 'role']);

        $response = [
            'success'=> true,
            'users'=> $users
        ];

        return response()->json($response, 200);
    }

    public function registerAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'role'=> 'required',
            'name'=> 'required',
            'email'=> 'required|email',
            'password'=> 'required|min:8',
            'confirm_password'=> 'required|same:password'
        ]);

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $findUser = User::where('email', strtolower($request->email))->exists();

        if($findUser){
            $response = [
                'success'=> false,
                'message'=> 'User already exists.'
            ];

            return response()->json($response, 401);
        }

        $input = $request->all();
        $input['email'] = strtolower($input['email']);
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);
        $count = 21000 + $user->id;
        $user->rocket_id = "RFA{$count}";
        $user->role = $request->role;
        $user->save();

        $response = [
            'success'=> true,
            'message'=> 'User registered successfully.'
        ];

        return response()->json($response, 200);
    }

    public function updateAdmin(Request $request){
        if($request->has('password')){
            $validator = Validator::make($request->all(), [
                'id'=> 'required',
                'role'=> 'required',
                'name'=> 'required',
                'email'=> 'required|email',
                'password'=> 'required|min:8',
                'confirm_password'=> 'required|same:password'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'id'=> 'required',
                'role'=> 'required',
                'name'=> 'required',
                'email'=> 'required|email'
            ]);
        }

        if($validator->fails()){
            $response = [
                'success'=> false,
                'message'=> $validator->errors()
            ];

            return response()->json($response, 400);
        }

        $user = User::find($request->id);

        if($request->has('password')){
            $input = $request->all();
            $input['email'] = strtolower($input['email']);
            $input['password'] = bcrypt($input['password']);

            $user->fill($input);
            $user->role = $request->role;
            $user->save();
        } else {
            $input = $request->all();
            $input['email'] = strtolower($input['email']);

            $user->fill($input);
            $user->role = $request->role;
            $user->save();
        }

        $response = [
            'success'=> true,
            'message'=> 'User updated successfully.'
        ];

        return response()->json($response, 200);
    }
}
