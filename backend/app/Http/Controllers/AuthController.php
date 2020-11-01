<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string']
        ]);

        $service = new AuthService($data);

        if (!$service->authenticate()) {
            return $service->asErrorResponse();
        }

        $user = $service->getModel();

        $token = $user->createToken(Str::slug($user->name));

        return new Response([
            'user' => $user,
            'token' => $token->plainTextToken,
        ], 200);
    }
}
