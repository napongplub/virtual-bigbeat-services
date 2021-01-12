<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;

class BackofficeAuthentication extends Controller {

    public function __construct() {
        config(['auth.defaults.guard' => 'backoffice']);
    }

    public function login(Request $request) {
        $request->validate([
            "email"    => "required|email",
            "password" => "required|min:6",
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt(['email' => $credentials['email'], 'password' => \base64_decode($credentials['password'])])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function me(Request $request) {
        if ($request->user()) {
            return $request->user();
        }

        return \response('Token is Invalid', 401);
    }

    public function logout() {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(["status" => true]);
        } catch (\Throwable $th) {
            throw $th;
        }

    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
            'user'         => auth()->user(),
        ]);
    }
}
