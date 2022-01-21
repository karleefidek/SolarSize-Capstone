<?php

namespace App\Http\Controllers;


//command pattern?
class ApiController extends Controller
{


    function respondWithData($response, $statusCode = 200, $headers = [])
    {
        return response()->json($response, $statusCode, $headers);
    }


    function respondWithError($response, $statusCode)
    {
        return $this->respondWithData(
            [
                'errors' => [
                    'message' => $response,
                    'status_code' => $statusCode
                ]
            ],
            $statusCode

        );
    }
}
