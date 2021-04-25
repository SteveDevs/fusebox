<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Panic;
use Validator;
use App\Http\Resources\PanicResource;
   
class PanicAPIController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {

        $user = DB::table('users')->where('id', '=', $request->get('user_id'))->first();
    
        return $this->sendResponse($user->topped, 'Topped retrieved successfully.');
    }


    public function store(Request $request)
    {

        $data = $request->all();

        $validator = Validator::make($data,[
            'topped' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $user = User::find($request->get('user_id'));
        $user->topped = $user->topped + $request->get('topped');
        $user->save();

        $transaction['type'] = 'topup';
        $transaction['amount'] = $request->get('topped');
        $transaction['user_id'] = $request->get('user_id');

        Transaction::create($transaction);

        return response(['topup' => new TopupResource($user->topped), 'message' => 'Created successfully'], 201);
    }

     public function update(Request $request)
    {

        $data = $request->all();

        $validator = Validator::make($data,[
            'topup' => 'required'
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $user = DB::table('users')->where('id', '=', $request->get('user_id'))->first();

        $new_topped = $user->topped + $request->get('topup');

        DB::table('users')->where('id', $request->get('user_id'))->update(array('topped' => $new_topped));
        $transaction['type'] = 'topup';
        $transaction['amount'] = $request->get('topup');
        $transaction['user_id'] = $request->get('user_id');

        Transaction::create($transaction);

        return response(['topup' => $user->topped, 'message' => 'Created successfully'], 201);
    }
}