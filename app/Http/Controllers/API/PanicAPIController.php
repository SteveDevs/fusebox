<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Panic;
use Validator;
use App\Http\Resources\PanicResource;
use App\Jobs\SendCancelPanicToWayneJob;
use App\Jobs\SendPanicToWayneJob;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Http;

class PanicAPIController extends BaseController
{

    public function getHistory(){
        $panics = Panic::all();

        return $this->sendResponse(['panics' => PanicResource::collection($panics)]);
    }

    public function store(Request $request)
    {
        //$token = $request->bearerToken();
        $data = $request->all();

        $validator = Validator::make($data,[
            'longitude' => 'required|string',
            'latitude' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("validation failure – missing/incorrect variables", 400);
        }
        $user = auth()->user();
        $data = [
            'user_id' => $user->id,
            'longitude' => $request->get('longitude'),
            'latitude' => $request->get('latitude'),
            'panic_type' => ($request->get('panic_type') !== null ) ? $request->get('panic_type') : '',
            'details' => ($request->get('details') !== null) ? $request->get('details') : '',
        ];

        $panic = Panic::create($data);

        $data['reference_id'] = $panic->id;
        $data['user_name'] = $user->name;
        unset($data['user_id']);
        
        SendPanicToWayneJob::dispatch($data);
        
        return $this->sendResponse(['panic_id' => $panic->id], "Panic raised successfully");
    }

     public function cancel(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data,[
            'panic_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError("validation failure – missing/incorrect variables", 400);
        }
        
        $panic = Panic::where('wayne_panic_id', $request->get('panic_id'))->first();
        if(!$panic){
            return $this->sendError("panic not found", 400);
        }

        SendCancelPanicToWayneJob::dispatch($panic->wayne_panic_id);
        $panic->delete();

        return $this->sendResponse((new \stdClass()), "Panic cancelled successfully");
    }
}