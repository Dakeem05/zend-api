<?php

namespace App\Services\Api\V1;

use App\Models\User;
use App\Traits\Api\V1\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthenticationService
{
    use ApiResponseTrait;

    public function auth (object $user_data)
    {
        $user = User::where('address', $user_data->address)->first();

        if ($user == null) {
            $_user = User::create([
                'address' => $user_data->address,
            ]);
            return $_user->address;
        }
        return $user->address;
    }

}

