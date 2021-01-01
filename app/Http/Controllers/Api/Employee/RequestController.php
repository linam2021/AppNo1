<?php

namespace App\Http\Controllers\Api\Employee;

use App\Models\Request;
use App\Models\Section;
use App\Traits\Messenger;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Status;
use App\Models\Suggestion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
class RequestController extends Controller
{
    use Messenger;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employee=Auth::user();
        $employeeRequests=DB::table('requests')
        ->join('users', 'requests.user_id', '=', 'users.id')
        ->select('requests.id','requests.created_at', 'users.f_name', 'users.s_name','users.t_name','users.l_name','users.email','requests.employee_id','requests.type')
        ->where('requests.section_id', '=', $employee->section_id)
        ->where('requests.employee_id','=', $employee->id)
        ->orWhereNull('requests.employee_id')
        ->get();
        if($employeeRequests->count()==0)
        {
             return $this->sendError('There is no requests for this employees');
        }
        return $this->sendResponse($employeeRequests, 'These requests were found');
    }

    public function show($type)
    {
        $employee=Auth::user();
        $employeeRequests=DB::table('requests')
        ->join('users', 'requests.user_id', '=', 'users.id')
        ->select('requests.id','requests.created_at', 'users.f_name', 'users.s_name','users.t_name','users.l_name','users.email','requests.type')
        ->where('requests.section_id', '=', $employee->section_id)
        ->where('requests.type','=',$type)
        ->where('requests.section_id', '=', $employee->section_id)
        ->where('requests.employee_id','=', $employee->id)
        ->orWhereNull('requests.employee_id')
        ->get();
        if($employeeRequests->count()==0)
        {
             return $this->sendError('There is no requests for this employees in this type');
        }
        return $this->sendResponse($employeeRequests, 'These requests were found');
    }
    public function update($id)
    {
        $request=Request::find($id);
        $emp_id=$request->employee_id;
        if (($request->employee_id != Auth::id()) && (!(is_null($emp_id)))) {
            return $this->sendError('You do not have rights to assign this request');
        }
        if(!(is_null($emp_id))){
            return $this->sendError('This request was previously assigned to this employee');
        }
        $request->employee_id =Auth::id();
        $request->save();
        return $this->sendResponse($request, 'This request is assigned to this successfully!');
    }
}
