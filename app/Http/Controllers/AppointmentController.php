<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Appointments;

class AppointmentController extends Controller
{
    public function appointment(Request $request){
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

        $existingAppointment = Appointments::find($request->rocket_id);

        if(!$existingAppointment){
            $carriers = ['aon', 'beyond', 'dual', 'flow', 'neptune', 'palomar', 'sterling', 'wright'];

            $appointment = new Appointments();

            $appointment->rocket_id = $request->rocket_id;
            $appointment->save();

            return response()->json(['success'=> true, 'appointments'=> $appointment], 200);
        } else {
            if($request->has('update')){
                $data = $request->update;

                $existingAppointment->fill($data);
                $existingAppointment->save();

                $response = [
                    'succes'=> true,
                    'message'=> 'Appointments updated successfully.',
                    'appointments'=> $existingAppointment
                ];

                return response()->json($response, 200);

            } else {
                return response()->json(['success'=> true, 'appointments'=> $existingAppointment], 200);
            }
        }
    }
}
