<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $credentials = $request->all();

        if (! $token = Auth::guard('employee-api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $response = [
            'success' => true,
            'token_type' => 'bearer',
            'access_token' => $token,
        ];
        return response()->json($response,401);
    }
}
