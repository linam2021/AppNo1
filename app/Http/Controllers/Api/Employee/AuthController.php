<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Traits\Messenger;


class AuthController extends Controller
{
    use Messenger;

    public function login(Request $request)
    {
        $credentials = $request->all();
        $validator = Validator::make($credentials,[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors(),"Login failed", 400);
        }


        if (! $token = Auth::guard('employee-api')->attempt($credentials)) {
            return $this->sendError(['Login error' => 'Incorrect email or password'],"Unauthorized",401);
            //return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = [
            'success' => true,
            'token_type' => 'bearer',
            'access_token' => $token,
        ];
        return response()->json($response,401);
    }
}
