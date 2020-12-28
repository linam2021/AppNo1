<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Traits\Messenger;
use App\Models\User;

class AuthController extends Controller
{
    use Messenger;
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username'=>'required',
            'email'=>'required|unique:users|email',
            'password'=>'required|string|min:8',
            'c_password'=>'required|same:password'
        ]);
        if($validator->fails())
        {
            return $this->sendError($validator->errors(),"Registration failed", 400);
        }

        $input =$request->all();
        $input['password']=Hash::make($input['password']);
        User::create($input);
        $response = [
            'success' => true,
            'message' => 'User registered Successfully!'
        ];
        return response()->json($response,201);
    }


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

        if (! $token = Auth::guard('user-api')->attempt($credentials)) {
            return $this->sendError(['Login error' => 'Incorrect email or password'],"Unauthorized",401);
        }

        $response = [
            'success' => true,
            'token_type' => 'bearer',
            'access_token' => $token,
        ];
        return response()->json($response,200);
    }
}
