<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'token'=>['required','string'],
        ]);
        $user=Auth::user();
        DeviceToken::updateOrCreate([
            'user_id'=>$user->id,
            'token'=>$request->token
        ]);
        return ApiResponse::getResponse(null,200,"token saved");
    }
}
