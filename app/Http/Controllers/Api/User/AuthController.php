<?php

namespace App\Http\Controllers\Api\User;

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
            'password' => 'required|string',
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
            'email' => ['filled','email',Rule::unique('users')->ignore(Auth::id())],
            'old_password' => 'filled|string',
            'new_password'=>'required_with:old_password|string',
            'f_name' => 'required|string',
            's_name' => 'required|string',
            't_name' => 'required|string',
            'l_name' => 'required|string',
            'date_of_birth' =>'required|date',
            'governorate' => 'required|string',
            'district' => 'required|string',
            'city' => 'required|string',
            'phone' => 'required|regex:/^[0-9]+$/',
            'gender' => 'required|string|in:male,female',
            'national_no' => 'required|regex:/^[0-9]+$/'
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",400);
        }

        if($request->old_password)
        {
            if(!Hash::check($request->old_password, $user->password))
            {
                return $this->sendError(['old_password'=>"old_password field should match your current password"] , "Unsuccessful",400);
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
            return $this->sendError(['error'=>$e->getMessage()] , "Unsuccessful",400);
        }


        return $this->sendResponse([],"logged out successfully");

    }
}
