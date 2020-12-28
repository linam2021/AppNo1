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

        //stored in database
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
        if($request->suggestions) //if the user sent a suggestion
        {
            Suggestion::create([
                'details' => $request->suggestions,
                'request_id' => $userRequest->id
            ]);
            //added for response only
            $userRequest->suggestions = $request->suggestions;
        }
        //added for response only
        $userRequest->status = 'Open';


        return $this->sendResponse($userRequest,"Request Stored Successfully");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userRequest = Request::find($id);

        if(is_null($userRequest))
        {
            return $this->sendError(['request' => 'no request with the specified id was found'] , 400);
        }

        //All the info about a request that
        //the api clients/users might need
        $response = [
            'type' => $userRequest->type,
            'subject' => $userRequest->subject,
            'details' => $userRequest->details,
            'suggestion' => $userRequest->suggestion->details,
        ];


        return $this->sendResponse($response,"Request added successfully");
    }


    public function rate(HttpRequest $request, $id)
    {
        $input = $request->all();
        $userRequest = Request::find($id);
        $validator = Validator::make( $input ,[
            'note' => 'required',
            'number' => 'required|min:1|max:5'
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",400);
        }
        if(is_null($userRequest))
        {
            return $this->sendError(['request' => 'no request with the specified id was found'] , 400);
        }
        if($userRequest->rating)
        {
            return $this->sendError(['rating' => 'you can only rate a request once'] , 400);
        }

        $rating = Rating::create([
            'number' => $request->number,
            'note' => $request->note,
            'request_id' => $userRequest->id
        ]);


        return $this->sendResponse($request,"Request has been rated successfully");
    }

}
