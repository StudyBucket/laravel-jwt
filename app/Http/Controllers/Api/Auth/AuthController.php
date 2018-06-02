<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\SignupUserRequest;

use JWTAuth;
use Auth;

use App\Jobs\User\StoreUser;
use App\Http\Resources\User\UserResource;

use App\Models\Auth\DeviceLogin;

class AuthController extends Controller
{
    /**
      * Login attempt
      *
      * @var Request $request
      *
      * @return response
      */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (! $token = JWTAuth::attempt($credentials)) {
            $response = [
                'data' => [
                  'error' => 'credentials'
                ]
            ];
            return response($response, 401);
        }
        $newDeviceLogin = new DeviceLogin;
        $newDeviceLogin->token = $token;
        $newDeviceLogin->agent = $request->header('User-Agent');
        $user = JWTAuth::toUser($token);
        $user->deviceLogins()->save($newDeviceLogin);
        $response = [
            'data' => [
                'token' => $token,
                //'user'  => $user
            ]
        ];
        return response($response, 200);
    }

    /**
      * Logout the current user
      *
      * @return response
      */
    public function logout()
    {
        $user = Auth::user();
        $token = JWTAuth::getToken();
        $deviceLogin = $user->deviceLogins()->where('token', $token)->first();
        $deviceLogin->delete();
        JWTAuth::invalidate($token);

        $response = [
            'data' => [
                //'message' => 'success'
                'user' => $user,
                //'token' => $token,
                //'deviceLogin' => $deviceLogin
            ]
        ];
        return response($response, 200);
    }

    /**
      * Logout all devices of the current user
      *
      * @return response
      */
    public function logoutAll()
    {
        $user = Auth::user();
        $token = JWTAuth::getToken();
        $deviceLogins = $user->deviceLogins()->get();
        // Invalidate all currently existing tokens for all devices of the user
        foreach ($deviceLogins as $device) {
          try {
            JWTAuth::invalidate($device->token);
          } catch(\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
            // token is allready invalidated
            continue;
          }
        }
        // Delete all records for former - now invalid - logins
        $user->deviceLogins()->delete();
        // Check weather there is something left ...
        $deviceLogins = $user->deviceLogins()->get();
        // respond sth
        $response = [
            'data' => [
                //'message' => 'success'
                'user' => $user,
                'deviceLogins' => $deviceLogins
            ]
        ];
        return response($response, 200);
    }

    /**
      * Login attempt
      *
      * @return response
      */
    public function refresh()
    {
        $token = JWTAuth::getToken();
        $newToken = JWTAuth::refresh($token);
        $response = [
            'data' => [
                'token' => $newToken
            ]
        ];
        return response($response, 200);
    }

    /**
      * Return data of the current logged-in user
      *
      * @var Request $request
      *
      * @return response
      */
    public function user(Request $request)
    {
        $response = new UserResource(Auth::user());
        return response($response, 200);
    }

    /**
      * Check
      *
      * @var Request $request
      *
      * @return response
      */
    public function check(Request $request)
    {
        if(Auth::user()){
            $response = ['data' => [
                    'message' => 'login valid'
                ]
            ];
            $status = 200;
        } else {
            $response = ['data' => [
                    'message' => 'login invalid'
                ]
            ];
            $status = 401;
        }
        return response($response, $status);
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signup(SignupUserRequest $request)
    {
        if (!JWTAuth::getToken()) { // Logged in user cannot perform this action
          dispatch(new StoreUser($request->all()));
          return response(null)
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }
}
