<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class TokenManagementController extends Controller
{
    public function generateToken()
    {
        $accessToken = encrypt(request('key').'__XXSEPRATEXX__'.Carbon::now()->addSeconds(3600));

        return response()->json([
            "access_token" => $accessToken,
            "token_type" => "Bearer",
            "expires_in" => 3600,
            "refresh_key" => request('key')
        ], 200);
    }
}
