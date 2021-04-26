<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
   
class AuthApiController extends BaseController
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $data = $request->all(); 

        $validator = Validator::make($data,[
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('validation failure – missing/incorrect variables', 400);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $data = [
                'api_access_token' => $user->createToken('MyApp')->accessToken,
            ];
            return $this->sendResponse($data);
        } 
        else{ 
            return $this->sendError('unauthorised', 401);
        } 
    }

}