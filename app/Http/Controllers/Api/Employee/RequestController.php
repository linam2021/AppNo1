<?php

namespace App\Http\Controllers\Api\Employee;

use App\Models\User;
use App\Models\Status;
use App\Models\Request;
use App\Models\Section;
use App\Models\Employee;
use App\Traits\Messenger;
use App\Models\Suggestion;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request as HttpRequest;

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
        $employeeId = Auth::id();
        $employee =Employee::where('id',$employeeId)->first();
        $employeeRequests=DB::table('requests')
        ->join('users','requests.user_id', '=','users.id')
        ->join('sections','requests.section_id','=','sections.id')
        ->select('requests.id','requests.created_at','requests.subject','requests.details','requests.employee_id','requests.type','sections.name as section','users.f_name', 'users.s_name','users.t_name','users.l_name','users.email as user_email')
        ->where ('requests.section_id','=', $employee->section_id)
        ->whereNotIn('requests.id', function ($query) {
            $query->select('statuses.request_id')
                  ->from('statuses')
                  ->where('statuses.name','=','Solved');})
        ->get();
        if($employeeRequests->count()==0)
        {
             return $this->sendResponse($employeeRequests,'There were no requests found for this employee');
        }
        foreach($employeeRequests as $request)
        {
            $request->status = Status::where('request_id',$request->id)->latest()->first();
            $request->section = Section::find($employee->section_id)->name;
        }
        return $this->sendResponse($employeeRequests, 'These requests were found');
    }

    public function filter(httpRequest $request)
    {
        $validator = Validator::make( $request->all() ,[
            'type' => 'string|required|in:complaint,suggestion,thanks',
        ]);
        if ($validator->fails())
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",400);

        $employeeId = Auth::id();
        $employee =Employee::where('id',$employeeId)->first();
        $employeeRequests=DB::table('requests')
        ->join('users','requests.user_id', '=','users.id')
        ->join('sections','requests.section_id','=','sections.id')
        ->select('requests.id','requests.created_at','requests.subject','requests.details','requests.employee_id','requests.type','sections.name as section','users.f_name', 'users.s_name','users.t_name','users.l_name','users.email as user_email')
        ->where('requests.type','=',$request->type)
        ->where ('requests.section_id','=', $employee->section_id)
        ->whereNotIn('requests.id', function ($query) {
            $query->select('statuses.request_id')
                  ->from('statuses')
                  ->where('statuses.name','=','Solved');})
        ->get();
        if($employeeRequests->count()==0)
        {
            return $this->sendResponse($employeeRequests,'There were no requests found for this employee');
        }
        foreach($employeeRequests as $request)
        {
            $request->status = Status::where('request_id',$request->id)->latest()->first();
            $request->section = Section::find($employee->section_id)->name;
        }
        return $this->sendResponse($employeeRequests, 'These requests were found');
    }

    public function show($id)
    {
        $employeeId = Auth::id();
        $employee =Employee::where('id',$employeeId)->first();
        $employeeRequest=DB::table('requests')
        ->join('users','requests.user_id', '=','users.id')
        ->join('sections','requests.section_id','=','sections.id')
        //->select('requests.id','requests.created_at','requests.subject','requests.details','requests.employee_id','requests.type','sections.name as section','users.f_name', 'users.s_name','users.t_name','users.l_name','users.email as user_email')
        ->select('requests.id','requests.type','requests.subject','requests.details','requests.employee_id','requests.created_at')
        ->where ('requests.section_id','=', $employee->section_id)
        ->where ('requests.id',$id)
        ->first();
        if(! $employeeRequest)
        {
            return $this->sendError(['not found' => 'request wasnt found'],'no such request was found');
        }
        $req=Request::where('id',$id)->first();
        $employeeRequest->suggestion = Suggestion::where('request_id', $employeeRequest->id)->first();
        $employeeRequest->user=User::where('id',$req->user_id)->first();
        $employeeRequest->status = Status::where('request_id',$id)->latest()->first();
        $employeeRequest->section = Section::find($employee->section_id);

        return $this->sendResponse($employeeRequest, 'This request was found');
    }

    public function update($id)
    {
        $request=Request::find($id);
        if (is_null($request))
           return $this->sendError('This request is not found');
        $emp_id=$request->employee_id;
        if (($request->employee_id != Auth::id()) && (!(is_null($emp_id)))) {
            return $this->sendError('You do not have rights to assign this request');
        }
        if(!(is_null($emp_id))){
            return $this->sendError('This request was previously assigned to this employee, and its status is active');
        }
        $request->employee_id =Auth::id();
        $request->save();
        $status = Status::create([
            'name' => 'Active',
            'request_id' => $id
        ]);
        $request->status = $status;
        return $this->sendResponse($request, 'This request is assigned to this employee successfully!');
    }

    public function changeStatustoSolved(httpRequest $request)
    {
        $req=Request::find($request->id);
        if (is_null($req))
            return $this->sendError('This request is not found');
        $isSolved=Status::where('request_id',$request->id)->where('name','Solved')->get();
        if($isSolved->count()!=0)
           return $this->sendError('This request was previously solved');
        $status = Status::create([
            'name' => 'Solved',
            'request_id' => $request->id
        ]);
        $req->status = $status;
        return $this->sendResponse($req, 'This request is solved successfully!');
    }
}
