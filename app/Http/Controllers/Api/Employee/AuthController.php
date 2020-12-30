<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

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

    public function employee()
    {
        $employee = Auth::user();

        return $this->sendResponse($employee,"This is current employee's information");
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'filled|email',
            'old_password' => 'filled',
            'new_password'=>'required_with:old_password',
            'f_name' => 'required',
            'l_name' => 'required',
            'region' => 'required',
            'city' => 'required',
            'town' => 'required',
        ]);

        if($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",400);
        }

        $employee = Auth::user();
        if($request->old_password)
        {
            if(!Hash::check($request->old_password, $employee->password))
            {
                return $this->sendError(['old_password'=>"old_password doesn't match current password"] , 400);
            }
            $request->password = Hash::make($request->new_password);
        }

        Employee::where('id', Auth::id())
            ->update($request->except(['old_password','new_password']));

        return $this->sendResponse($employee,"This is the current employee's updated information");
    }
}
