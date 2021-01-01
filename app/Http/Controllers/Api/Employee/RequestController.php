<?php

namespace App\Http\Controllers\Api\Employee;

use App\Models\Request;
use App\Models\Section;
use App\Traits\Messenger;
use App\Http\Controllers\Controller;
use App\Models\Status;
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
        $employeeRequests=DB::table('requests')
        ->join('users','requests.user_id', '=','users.id')
        ->join('sections','requests.section_id','=','sections.id')
        ->select('requests.id','requests.created_at','users.f_name', 'users.s_name','users.t_name','users.l_name','users.email','requests.employee_id','requests.type','sections.name')
        ->get();
        if($employeeRequests->count()==0)
        {
             return $this->sendError('There is no request');
        }
        return $this->sendResponse($employeeRequests, 'These requests were found');
    }

    public function filter(httpRequest $request)
    {
         $employeeRequests=DB::table('requests')
        ->join('users','requests.user_id', '=','users.id')
        ->join('sections','requests.section_id','=','sections.id')
        ->select('requests.id','requests.created_at','users.f_name', 'users.s_name','users.t_name','users.l_name','users.email','requests.employee_id','requests.type','sections.name')
        ->where('requests.type','=',$request->type)
        ->get();
        if($employeeRequests->count()==0)
        {
             return $this->sendError('There is no request');
        }
        return $this->sendResponse($employeeRequests, 'These requests were found');
    }

    public function show($id)
    {
        // $employeeId = Auth::id();
        // $employeeRequest = Request::where('employee_id',$employeeId)->where('id',$id)->first();

        // if(is_null($employeeRequest))
        // {
        //     return $this->sendError(['request' => 'no request with the specified id was found'] , 400);
        // }
        $employeeRequest = Request::where('id',$id)->first();
        $employeeRequest->suggestion = $employeeRequest->suggestion;
        $employeeRequest->status = Status::where('request_id',$employeeRequest->id)->latest()->first();
        $employeeRequest->section = Section::find($employeeRequest->section_id);

        return $this->sendResponse($employeeRequest,"Request retrieved successfully");
    }

    public function update($id)
    {
        $request=Request::find($id);
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
        return $this->sendResponse($request, 'This request is assigned to this successfully!');
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
