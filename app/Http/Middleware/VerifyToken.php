<?php

namespace App\Http\Middleware;

use App\Models\ApiKeys;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        request()->validate([
            'key' => 'required'
        ]);

        if(!ApiKeys::where(['key' => request('key'), 'status' => 1])->exists()) {
            return response()->json([
                "error" => "Invalid key provided.",
            ], 422);
        }

        return $next($request);
    }
}
