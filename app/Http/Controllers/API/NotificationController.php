<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifications;

class NotificationController extends Controller
{
    public function index(){
        return Notifications::orderBy('created_at', 'DESC')->get();
    }

    public function add(Request $request){
        $notification = new Notifications();
        $notification->rocket_id = $request->user()->rocket_id;
        $notification->agency_name = $request->agency_name;

        $notification->save();

        return response()->json(['success'=> true], 200);
    }

    public function read(Request $request){
        $notification = Notifications::find($request->rocket_id);
        $notification->read = true;
        $notification->save();

        return response()->json(['success'=>true], 200);
    }
}
