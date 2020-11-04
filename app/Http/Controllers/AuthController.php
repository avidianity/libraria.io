<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
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

    public function register(Request $request)
    {
        return $this->create($request, User::NORMAL);
    }

    public function registerAsAdmin(Request $request)
    {
        return $this->create($request, User::ADMIN);
    }

    protected function create(Request $request, $role)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:' . User::class],
            'password' => ['required', 'string', 'min:6', 'max:255'],
        ]);

        $data['password'] = Hash::make($data['password']);

        $user = new User($data);
        $user->assignRole($role);
        $user->save();

        $response = [
            'user' => $user,
        ];

        if ($role === User::NORMAL) {
            $token = $user->createToken(Str::slug($user->name));
            $response['token'] = $token->plainTextToken;
        }

        return new Response($response, 201);
    }
}
