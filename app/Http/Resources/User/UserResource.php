<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\Resource;
use Auth;

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
        // return parent::toArray($request);

        // Check if the current data set is not the current Auth::user()
        if( !$this->isCurrentAuthUser() ){
          // Check the current data sets privacy settings
          if(!$this->isPropertyPublic('email')) $this->email = NULL;
        }
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'since'             => $this->created_at,
        ];
    }
}
