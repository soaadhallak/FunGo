<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Models\DeviceToken;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    //register new user
    public function register(RegisterRequest $request){
      $user=User::create($request->validated());
      $token=$user->createToken('user_token')->plainTextToken;
      $user->assignRole('user');
      $user['token']=$token;
        return ApiResponse::getResponse($user ,201,'Registered Successfully');
    }
    //login user
    public function login(LoginRequest $request){
        $request->validated();
        if(!FacadesAuth::attempt($request->only('email','password'))){
            return ApiResponse::getResponse(null,401,'Invalid Login Information');
        }
        $user=User::where('email',$request->email)->firstOrFail();
        $token=$user->createToken('user_token')->plainTextToken;
        $user['token']=$token;
        return ApiResponse::getResponse($user,201,'Logged in Successfully');
    }
    //logout user
    public function logout(Request $request){
        $user=$request->user();
        $deviceToken=$request->input('device_token');
        if($deviceToken){
            DeviceToken::where('user_id',$user->id)
            ->where('token',$deviceToken)->delete();
        }
        $user->currentAccessToken()->delete();
        return ApiResponse::getResponse(null,200,'Logged out successfully');

    }
}