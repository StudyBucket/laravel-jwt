<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'array',
        'privacy' => 'array',
    ];

    public function setPasswordAttribute($pass)
    {
        //$this->attributes['password'] = Hash::make($pass);
        $this->attributes['password'] = bcrypt($pass);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Here is where you can specify relationships for this model.
    |
    */

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users')->withPivot('incepts_at', 'expires_at');
    }

    public function deviceLogins()
    {
        return $this->hasMany('App\Models\Auth\DeviceLogin');
    }

    /*
    |--------------------------------------------------------------------------
    | Custom functions
    |--------------------------------------------------------------------------
    |
    | Here is where you can implement custom functions for this model.
    |
    */

    /**
     * Checks if User has access to $permissions.
     */
    public function hasAccess(array $permissions) : bool
    {
        // check if the permission is available in any role
        foreach ($this->roles as $role) {
            if($role->hasAccess($permissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Checks if the user belongs to role.
     */
    public function inRole(string $roleSlug)
    {
        return $this->roles()->where('slug', $roleSlug)->count() == 1;
    }

    /**
     * Checks if a property is visible to the public.
     *
     * @return bool
     */
    public function isPropertyPublic(string $property) : bool
    {
        if(!$this->privacy[$property] || $this->privacy[$property] != 'public') {
          return false;
        }
        return true;
    }

    /**
     * Checks if Auth::user() matches $this.
     *
     * @return bool
     */
    public function isCurrentAuthUser() : bool
    {
        if(Auth::user()->id != $this->id) {
          return false;
        }
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Here is where you can register validation rules for this model.
    |
    */

    /**
     * Returns models validation rules
     *
     * @param usage - determins the request type
     */
    public function modelRules($usage)
    {
        $rules = [
            'store' => [
                'name' => 'required|string|unique:users|min:3',
                //'firstname' => 'required|string|min:3',
                //'lastname' => 'required|string|min:3',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|max:10',
                //'profile_image' =>'sometimes|file|mimes:jpeg,jpg,bmp,png|nullable'
            ],
            'update' => [
                'name' => 'string|unique:users,name,'.$this->id.',id',
                //'firstname' => 'string|min:3',
                //'lastname' => 'string|min:3',
                'email' => 'email|unique:users,email,'.$this->id.',id',
                'password' => 'string|min:6|max:10',
                //'profile_image' =>'sometimes|file|mimes:jpeg,jpg,bmp,png|nullable'
            ]
        ];
        return $rules[$usage];
    }
}
