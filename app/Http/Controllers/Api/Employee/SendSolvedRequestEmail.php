<?php

namespace App\Http\Controllers\Api\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use App\Mail\SendMail;
use App\Traits\Messenger;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SendSolvedRequestEmail extends Controller
{
    use Messenger;

    public function sendEmail(Request $request)  // this is most important function to send mail and inside of that there are another function
    {
        if (!$this->validateEmail($request->email)) {  // this is validate to fail send mail or true
            return $this->failedResponse();
        }
        $this->send($request->email);  //this is a function to send mail
          return $this->successResponse();
    }

    public function send($email)  //this is a function to send mail
    {
        Mail::to($email)->send(new SendMail());  // token is important in send mail
    }


    public function validateEmail($email)  //this is a function to get your email from database
    {
        return !!User::where('email', $email)->first();
    }

    public function failedResponse()
    {
        return $this->sendError(
            ['error' => 'Email wasn\'t found in our database']);
    }

    public function successResponse()
    {
        return $this->sendResponse('','Reset Email was sent successfully, please check your inbox.');
    }
}
