<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Membership;

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

    public function setPasswordAttribute($pass)
    {
        //$this->attributes['password'] = Hash::make($pass);
        $this->attributes['password'] = bcrypt($pass);
    }

    public function membership()
    {
        return $this->hasOne(Membership::class, 'user_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_users')->withPivot('incepts_at', 'expires_at');
    }

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
     * Returns models validation rules
     *
     * @param usage - determins the request type
     */
    public function modelRules($usage)
    {
        $rules = [
            'store' => [
                'name' => 'required|string|unique:users|min:3',
                'firstname' => 'required|string|min:3',
                'lastname' => 'required|string|min:3',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|max:10',
                'profile_image' =>'sometimes|file|mimes:jpeg,jpg,bmp,png|nullable'
            ],
            'update' => [
                'name' => 'string|unique:users,name,'.$this->id.',id',
                'firstname' => 'string|min:3',
                'lastname' => 'string|min:3',
                'email' => 'email|unique:users,email,'.$this->id.',id',
                'password' => 'string|min:6|max:10',
                'profile_image' =>'sometimes|file|mimes:jpeg,jpg,bmp,png|nullable'
            ]
        ];
        return $rules[$usage];
    }
}
