<?php

namespace App\Http\Controllers\Api\Employee;

use Exception;
use App\Models\Employee;
use App\Traits\Messenger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


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

    public function employee()
    {
        $employee = Auth::user();

        return $this->sendResponse($employee,"This is current employee's information");
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => ['filled','email',Rule::unique('users')->ignore(Auth::id())],
            'old_password' => 'filled',
            'new_password'=>'required_with:old_password',
            'f_name' => 'filled',
            'l_name' => 'filled',
            'governorate' => 'filled',
            'district' => 'filled',
            'city' => 'filled',
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct","Unsuccessful",400);
        }

        $employee = Auth::user();
        if($request->old_password)
        {
            if(!Hash::check($request->old_password, $employee->password))
            {
                return $this->sendError(['old_password'=>"old_password field should match your current password"] ,"Unsuccessful",400);
            }
            $employee->password = Hash::make($request->new_password);
        }

        $employee->update($request->except(['old_password','new_password']));

        return $this->sendResponse($employee,"This is the current employee's updated information");
    }

    public function logout()
    {
        try{
            Auth::logout();
        }
        catch(Exception $e)
        {
            return $this->sendError("[$e->getMessage()]" , "Unsuccessful",400);
        }

        return $this->sendResponse([],"logged out successfully");

    }
}
