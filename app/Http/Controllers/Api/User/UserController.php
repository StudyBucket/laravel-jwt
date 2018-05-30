<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Config;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

use App\Models\User;
use App\Jobs\User\StoreUser;
use App\Jobs\User\UpdateUser;
use App\Jobs\User\DestroyUser;

use App\Http\Resources\User\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('index', User::class)) {
          $response = User::paginate(Config::get('pagination.itemsPerPage'));
          $response = UserResource::collection($response)
                        ->appends('paged', $request->input('paged'));
          return response($response)
                    ->setStatusCode(200);
        } else {
          return response('{"message:"}')
                    ->setStatusCode(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        if (Auth::user()->can('create', User::class)) {
          dispatch(new StoreUser($request->all()));
          return response(null)
                    ->setStatusCode(201);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        if (Auth::user()->can('update', $user)){
          dispatch(new UpdateUser($user, $request->all()));
          return response(User::find($user->id))
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (Auth::user()->can('delete', $user)){
          dispatch(new DestroyUser($user));
          return response(null)
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }
}
