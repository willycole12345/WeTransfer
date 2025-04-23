<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function create(Request $request){

        $response = array();
        
         $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);
 
    $user = User::where('email', $request->email)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        
          $response['message'] = 'The provided credentials are incorrect.';
          $response['status']= 'failed';
        
    }
 
       $response['message']=  $user->createToken($request->email)->plainTextToken;
       $response['userrecord']= $user;
       $response['status'] = 'success';

       return new LoginResource($response);
    }
}
