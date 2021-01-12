<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Model\Register;
use App\Model\VisitorLogin;
use Auth;

class RegisterAuthentication extends Controller
{

    public function __construct()
    {
        config(['auth.defaults.guard' => 'register']);
    }

    public function login(Request $request)
    {
        $request->validate([
            "email"    => "required|email",
            "password" => "required|min:6",
        ]);

        $credentials = $request->only('email', 'password');
        $ip          = $request->ip();
        $user_agent  = $request->server('HTTP_USER_AGENT');

        if (!$token = auth()->attempt(['email' => $credentials['email'], 'password' => \base64_decode($credentials['password'])])) {
            $this->createLog($ip, $user_agent, "N");
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $this->createLog($ip, $user_agent, "Y",Auth::user()->id);
        return $this->createNewToken($token);
    }

    public function loginSpeaker(Request $request)
    {
        $request->validate([
            "email"    => "required|email",
            "password" => "required|min:6",
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt(['email' => $credentials['email'], 'password' => \base64_decode($credentials['password']), "is_speaker" => "Y"])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(["status" => true]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function me(Request $request)
    {
        // if($request->user()){
        //     return $request->user();
        // }

        // return \response('Token is Invalid',401);
        $user =  $request->user();
        if ($user) {
            $userId = $user->id;
            $userData = Register::with(['mainCate', 'timeZone'])->findOrFail($userId);

            return $userData;
        } else {
            return \response(null, 401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
            'user'         => auth()->user(),
        ]);
    }
    private function createLog($ip, $userAgent, $success, $userId = null) {
        $log               = new VisitorLogin();
        $log->ip           = $ip;
        $log->user_agent   = $userAgent;
        $log->success      = $success;
        $log->visitor_id = $userId;
        $log->save();
    }
}
