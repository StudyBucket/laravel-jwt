<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\Resource;
use Auth;

use App\Http\Resources\Role\RoleResource;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $user = $this->applyVisibilityRules();
        // return parent::toArray($request);
        return [
            'id'                => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'since'             => $user->created_at,
            'roles'             => RoleResource::collection($user->roles)
        ];
    }

    /**
     * Returns a copy of $this cleaned up according to privacy settings of the particular user.
     *
     * @return copy of $this (User)
     */
    private function applyVisibilityRules(){
        $user = $this;
        // Check if the current data set is not the current Auth::user()
        if( !$user->isCurrentAuthUser() ){
          // Check the current data sets privacy settings
          if(!$user->isPropertyPublic('email')) $this->email = NULL;
        }
        return $user;
    }
}
