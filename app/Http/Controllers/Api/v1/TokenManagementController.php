<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\ApiKeys;
use Carbon\Carbon;

class TokenManagementController extends Controller
{
    public function generateToken()
    {
        request()->validate([
            'key' => 'required'
        ]);

        if(!ApiKeys::where(['key' => request('key'), 'status' => 1])->exists()) {
            return response()->json([
                "error" => "Invalid key provided.",
            ], 422);
        }

        $accessToken = encrypt(request('key').'__XXSEPRATEXX__'.Carbon::now()->addSeconds(3600));

        return response()->json([
            "access_token" => $accessToken,
            "token_type" => "Bearer",
            "expires_in" => 3600,
            "refresh_key" => request('key')
        ], 200);
    }
}
