<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RegisterUserRequest;
use App\Services\Api\V1\AuthenticationService;
use App\Traits\Api\V1\ApiResponseTrait;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function auth (RegisterUserRequest $request, AuthenticationService $auth_service)
    {
        $_data = (Object) $request->validated();

        $request = $auth_service->auth($_data);
        
        return $this->successResponse([
            "user" => $request
        ], "User authenticated");
    }
}
