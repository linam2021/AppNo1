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

        foreach($userRequests as $request)
        {
            $request->suggestion = $request->suggestion == null ? '' : $request->suggestion;
            $request->status = Status::where('request_id',$request->id)->latest()->first();
            $request->rating = $request->rating;
            $request->section = Section::where('name',$request->section);
        }
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
            'section' => 'required',
            'suggestion' => 'filled', //must not be empty when it is present.
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",code:400);
        }

        $section = Section::where('name',$request->section);
        if(!$section)
        {
            return $this->sendError(['section' => 'no section with the specified name was found'] , code:400);
        }
        $userId = Auth::id();

        //stored in database
        $userRequest = Request::create([
            'type' => $request->type,
            'subject' => $request->subject,
            'details' => $request->details,
            'section_id' => $section->id,
            'user_id' => $userId
        ]);

        //default status
        $status = Status::create([
            'name' => 'Open',
            'request_id' => $userRequest->id
        ]);
        if($request->suggestion) //if the user sent a suggestion
        {
            $suggestion = Suggestion::create([
                'details' => $request->suggestion,
                'request_id' => $userRequest->id
            ]);
        }
        //added for response only
        $userRequest->suggestion = $suggestion;
        $userRequest->status = $status;
        $userRequest->section = $section;

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
        $userId = Auth::id();
        $userRequest = Request::where('user_id',$userId)->where('id',$id)->first();

        if(is_null($userRequest))
        {
            return $this->sendError(['request' => 'no request with the specified id was found'], code:400);
        }

        $userRequest->suggestion = $userRequest->suggestion;
        $userRequest->status = Status::where('request_id',$userRequest->id)->latest()->first();
        $userRequest->rating = $userRequest->rating;
        $userRequest->section = Section::find($userRequest->section_id);

        return $this->sendResponse($userRequest,"Request retrieved successfully");
    }


    public function rate(HttpRequest $request, $id)
    {
        $input = $request->all();
        $userId = Auth::id();
        $userRequest = Request::where('user_id',$userId)->where('id',$id)->first();
        if(is_null($userRequest))
        {
            return $this->sendError(['request' => 'no request with the specified id was found, or you are not authorized to access it'] , 400);
        }

        $validator = Validator::make( $input ,[
            'note' => 'required',
            'number' => 'required|min:1|max:5'
        ]);

        if ($validator->fails())
        {
            return $this->sendError($validator->errors(), "Make sure all paramaters are correct",code:400);
        }
        if(is_null($userRequest))
        {
            return $this->sendError(['request' => 'no request with the specified id was found'] , code:400);
        }
         //$userRequest->rating is not workng correctly for some reason
        if(Rating::where('request_id',$userRequest->id)->first())
        {
            return $this->sendError(['rating' => 'you can only rate a request once'] , code:400);
        }

        $rating = Rating::create([
            'number' => $request->number,
            'note' => $request->note,
            'request_id' => $userRequest->id
        ]);

        $userRequest->suggestion = $userRequest->suggestion;
        $userRequest->status = Status::where('request_id',$userRequest->id)->latest()->first();
        $userRequest->section = Section::find($userRequest->section_id);
        $userRequest->rating = $rating;

        return $this->sendResponse($userRequest,"Request has been rated successfully");
    }

}
