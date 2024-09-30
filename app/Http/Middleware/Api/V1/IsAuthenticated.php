<?php

namespace App\Http\Middleware\Api\V1;

use App\Models\User;
use App\Traits\Api\V1\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAuthenticated
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Retrieve the address from the route parameters
        $address = $request->route('address');

        $user = User::where('address', $address)->first();
        if($user == null){
            return $this->serverErrorResponse('Unauthorized access', Response::HTTP_FORBIDDEN);
        } else {
            return $next($request);
        }
    }
}
