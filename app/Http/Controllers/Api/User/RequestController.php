<?php

namespace App\Http\Controllers\Api\User;

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
        $userId = Auth::id();
        $userRequests = Request::where('user_id',$userId)->get();

        return $this->sendResponse($userRequests, 'These requests were found');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HttpRequest $request)
    {
        // 'type',
        // 'subject',
        // 'details',
        // 'user_id',
        // 'section_id'
        $input = $request->all();
        $validator = Validator::make( $input ,[
            'type' => 'required|in:complaint,suggestion,thanks',
            'subject' => 'required',
            'details' => 'required',
            //optional 'suggestions'
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",400);
        }
        $sectionId = Section::find(1)->id; //default section
        $userId = Auth::id();



        $userRequest = Request::create([
            'type' => $request->type,
            'subject' => $request->subject,
            'details' => $request->details,
            'section_id' => $sectionId,
            'user_id' => $userId
        ]);

        //default status
        Status::create([
            'name' => 'Open',
            'request_id' => $userRequest->id
        ]);
        if($request->suggestions) //not null
        {
            Suggestion::create([
                'details' => $request->suggestions,
                'request_id' => $userRequest->id
            ]);
        }

        return $this->sendResponse($userRequest,"Request Stored Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($userRequest)
    {
        if(is_null($userRequest))
        {
            return $this->sendError(['Request' => 'No request with the specified id was found'] , 400);
        }


        return $this->sendResponse($userRequest,"Request added successfully");
    }


    public function rate(HttpRequest $request, $userRequest)
    {
        $input = $request->all();
        $validator = Validator::make( $input ,[
            'rating' => 'required|min:1|max:5'
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",400);
        }
        if(is_null($userRequest))
        {
            return $this->sendError(['Request' => 'No request with the specified id was found'] , 400);
        }
        if($userRequest->rating)
        {
            return $this->sendError(['Rating' => 'You can only rate a request once'] , 400);
        }




        return $this->sendResponse($userRequest,"Request added successfully");
    }

}
