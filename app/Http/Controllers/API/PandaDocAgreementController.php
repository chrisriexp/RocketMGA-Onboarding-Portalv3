<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\onboardingInfo;
use App\Models\FileUpload;
use App\Models\Notifications;
use App\Models\Approval;
use App\Models\Logins;
use App\Models\MGALogins;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PandaDocAgreementController extends Controller
{
    public function completed(Request $request){
        $client = new \GuzzleHttp\Client();

        // Get Document ID
        $data = json_decode($request->getContent(), true);
        $document_id = $data[0]['data']['id'];
        $download_path = 'https://api.pandadoc.com/public/v1/documents/'.$document_id.'/download';

        // Get User from  Document ID
        $user = onboardingInfo::where('document_id', $document_id)->first();
        // Get User Rocket ID
        $rocket_id = $user->rocket_id;

        // File Name
        $file_name = time().'_agreement_'.$rocket_id. '.pdf';
    
        // Get Agreement Document from Pandadoc API
        $pandadocAgreementFile = $client->request('GET', $download_path, [
            'headers' => [
                'Authorization'=> 'API-Key 861fd4711f09ecbcf3f10f7a5cd449e22453abf3',
                'accept' => 'application/json',
            ],
            'sink' => storage_path('/app/public/uploads/'.$file_name)
        ]);

        // Save Agreement to file_uploads Database
        $fileUpload = new FileUpload;
        $fileUpload->user = $rocket_id;
        $fileUpload->type = 'agreement';
        $fileUpload->name = $file_name;
        $fileUpload->path = '/storage/uploads/' . $file_name;
        $fileUpload->save();

        // Save File ID
        $file_id = $fileUpload->id;

        // Update User Stage, Completed Status, and Agreement ID
        $user->completed = true;
        $user->stage = 'submitted';
        $user->agreement = $file_id;
        $user->save();

        // Create Notification for Agency
        $notification = new Notifications();
        $notification->rocket_id = $rocket_id;
        $notification->agency_name = $user->agency_name;
        $notification->save();

        // Create Approval Checklist for Agency
        $newApproval = new Approval();
        $newApproval->rocket_id = $rocket_id;
        $newApproval->save();

        // Create Login Table for Agency
        $newLogins = new Logins();
        $newLogins->rocket_id = $rocket_id;
        $newLogins->save();

        // Update/Create Agency Information in Zoho
        $zohoUpdate = Http::post('https://hooks.zapier.com/hooks/catch/14170682/bjht2i9/', [
            "agency_name"=> $user->agency_name,
            "agent_name"=> $user->agent_name,
            "phone"=> $user->phone,
            "email"=> $user->email,
            "adress"=> $user->address1." ".$user->address2,
            "city"=> $user->city,
            "state"=> $user->state,
            "zip"=> $user->zip,
            "npn"=> $user->agent_npn,
            "source"=> "Onboarding Portal"
        ]);

        // Get PandaDoc Agreement Details
        $pandadocAgreementDetails = $client->request('GET', 'https://api.pandadoc.com/public/v1/documents/S5iKeSTNPAzZQgXgTX4hvR/details', [
            'headers' => [
                'Authorization'=> 'API-Key 861fd4711f09ecbcf3f10f7a5cd449e22453abf3',
                'accept' => 'application/json',
            ]
        ]);

        $agreementDetails = json_decode($pandadocAgreementDetails->getBody()->getContents());

        $roster = [];

        // Roster
        for($i = 0; $i < 30; $i++){
            $item = [];

            foreach($agreementDetails->fields as $field ){
                if($field->field_id == 'rosterName'.$i || $field->field_id == 'rosterEmail'.$i){
                    if($field->value == ""){
                        break 2;
                    } else {
                        $key = $field->field_id == 'rosterName'.$i ? 'name' : 'email';
                        $item[$key] = $field->value;
                    }
                }
            }

            $item['role'] = $i == 0 ? 'admin' : 'agent';
            $item['rocket_id'] = $rocket_id;

            array_push($roster, $item);
        }

        // Add Roster to DB 
        foreach($roster as $agent){
            $mgaUser = new MGALogins();
            $mgaUser->fill($agent);
            $mgaUser->password = Str::random(9);
            $mgaUser->save();
        }

        $response = [
            'success'=> true,
            'message'=> 'Application completion successful.',
            'user'=> $user
        ];

        return response()->json($response, 200);
    }
}
