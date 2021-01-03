<?php

namespace App\Http\Controllers\Api\user;

//use Illuminate\Http\Request;

use App\Http\Requests\UpdatePasswordRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Traits\Messenger;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    use Messenger;

    public function passwordResetProcess(UpdatePasswordRequest $request){
        return $this->updatePasswordRow($request)->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();
      }

      // Verify if token is valid
      private function updatePasswordRow($request){
         return DB::table('password_resets')->where([
             'email' => $request->email,
             'token' => $request->resetToken
         ]);
      }

      // Token not found response
      private function tokenNotFoundError() {
          return $this->sendError(['error' => 'Either your email or token is wrong.'], code:422);
      }

      // Reset password
      private function resetPassword($request) {
          // find email
          $userData = User::whereEmail($request->email)->first();
          // update password
          $userData->update([
            'password'=>Hash::make($request->password)
          ]);
          // remove verification data from db
          $this->updatePasswordRow($request)->delete();

          // reset password response
          return $this->sendResponse('', 'Password has been updated.');
      }
}
