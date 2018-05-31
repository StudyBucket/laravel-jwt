<?php

namespace App\Http\Controllers\Api\Role;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Config;

use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Requests\Role\UserAttachRoleRequest;
use App\Http\Requests\Role\UserDetachRoleRequest;

use App\Models\Role;
use App\Jobs\Role\StoreRole;
use App\Jobs\Role\UpdateRole;
use App\Jobs\Role\DestroyRole;
use App\Jobs\User\AttachRole;
use App\Jobs\User\DetachRole;

use App\Http\Resources\Role\RoleResource;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->can('index', Role::class)) {
          $response = Role::paginate(Config::get('pagination.itemsPerPage'));
          $response = RoleResource::collection($response)
                        ->appends('paged', $request->input('paged'));
          return response($response)
                    ->setStatusCode(200);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        if (Auth::user()->can('create', Role::class)) {
          dispatch(new StoreRole($request->all()));
          return response(null)
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        if (Auth::user()->can('view', $role)) {
          $response = new RoleResource($role);
          return response($response)
                    ->setStatusCode(200);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        if (Auth::user()->can('update', $role)){
          dispatch(new UpdateRole($role, $request->all()));
          return response(Role::find($role->id))
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if (Auth::user()->can('delete', $role)){
          dispatch(new DestroyRole($role));
          return response(null)
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Associate a certain role to a user
     *
     * @param  \App\Requests\User\UserRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function attachToUser(UserAttachRoleRequest $request)
    {
        if (Auth::user()->can('associate', Role::class)){
          dispatch(new AttachRole(Auth::user(), $request->all()));
          return response(null)
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

    /**
     * Dissociate a certain role from a user
     *
     * @param  \App\Requests\User\UserRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function detachFromUser(UserDetachRoleRequest $request)
    {
        if (Auth::user()->can('associate', Role::class)){
          dispatch(new DetachRole(Auth::user(), $request->all()));
          return response(null)
                    ->setStatusCode(202);
        } else {
          return response(null)
                    ->setStatusCode(403);
        }
    }

}
