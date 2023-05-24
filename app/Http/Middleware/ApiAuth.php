<?php

namespace App\Http\Middleware;

use App\Models\ApiKeys;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationHeader = $request->header('Authorization');

        try {
            if ($authorizationHeader && strpos($authorizationHeader, 'Bearer ') === 0) {
                $token = decrypt(substr($authorizationHeader, 7));
                $time = Carbon::parse(explode('__XXSEPRATEXX__', $token)[1])->timestamp ?? Carbon::now()->subSeconds(3600)->timestamp;
                if(ApiKeys::where(['key' => explode('__XXSEPRATEXX__', $token)[0], 'status' => 1])->exists() && Carbon::now()->timestamp < $time) {
                    return $next($request);
                }
            }
        } catch(Exception $e) {
            //
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
