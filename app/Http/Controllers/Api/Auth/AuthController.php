<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\SignupUserRequest;

use JWTAuth;
use Auth;

use App\Jobs\User\StoreUser;
use App\Http\Resources\User\UserResource;

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
        $response = [
            'data' => [
                'token' => $token
            ]
        ];
        return response($response, 200);
    }

    /**
      * Logout the current user
      *
      * @return response
      */
    public function logout($passedToken = null)
    {
        if($passedToken){
            JWTAuth::invalidate($passedToken);
            // TODO: Error Handling!!
        } else {
            JWTAuth::invalidate();
        }
        $response = [
            'data' => [
                'message' => 'success'
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
