<?php


namespace App\Http\Controllers\API;


use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = null, $code = null)
    {
        $response = [
            'status' => "success",
            'message' => $message ?? "Action completed successfully",
            'data'    => $result,
        ];

        return response()->json($response, 200);
    }


    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $code = 404)
    {
        $response = [
            'status' => "error",
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
    
}