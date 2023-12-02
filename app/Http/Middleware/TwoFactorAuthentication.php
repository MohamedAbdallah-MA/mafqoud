<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthentication
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if (is_null($user->phone_verified_at))
        {
            return $this->apiResponse(401 , 'unauthorized' , 'unverified phone number');               
        }
        return $next($request);
    }
}
