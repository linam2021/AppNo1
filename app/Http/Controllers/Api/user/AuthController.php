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
use Exception;

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

    public function user()
    {
        $user = Auth::user();

        return $this->sendResponse($user,"This is current user's information");
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(),[
            'username'=> 'filled',
            'email' => ['filled','email',Rule::unique('users')->ignore(Auth::id())],
            'old_password' => 'filled',
            'new_password'=>'required_with:old_password',
            'f_name' => 'required',
            's_name' => 'required',
            't_name' => 'required',
            'l_name' => 'required',
            'region' => 'required',
            'city' => 'required',
            'town' => 'required',
            'phone' => 'required',
            'gender' => 'required|in:male,female',
            'national_no' => 'required'
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",code:400);
        }

        if($request->old_password)
        {
            if(!Hash::check($request->old_password, $user->password))
            {
                return $this->sendError(['old_password'=>"old_password field should match your current password"] , code:400);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->update($request->except(['old_password','new_password']));

        return $this->sendResponse($user,"This is the current user's updated information");
    }

    public function logout()
    {
        try{
            Auth::logout();
        }
        catch(Exception $e)
        {
            return $this->sendError("[$e->getMessage()]" , code:400);
        }


        return $this->sendResponse([],"logged out successfully");

    }
}
