<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 'slug', 'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Here is where you can specify relationships for this model.
    |
    */

    public function users()
    {
        return $this->belongsToMany(User::class, 'roles_users')->withPivot('incepts_at', 'expires_at');
    }

    /*
    |--------------------------------------------------------------------------
    | Custom functions
    |--------------------------------------------------------------------------
    |
    | Here is where you can implement custom functions for this model.
    |
    */

    public function hasAccess(array $permissions) : bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission))
                return true;
        }
        return false;
    }

    private function hasPermission(string $permission) : bool
    {
        return $this->permissions[$permission] ?? false;
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
                'name' => 'required|string|unique:roles|min:3|max:24',
                'slug' => 'required|string|unique:roles|min:3|max:18',
                'permissions' =>'json',
            ],
            'update' => [
                'name' => 'string|unique:users,name,'.$this->id.',id',
                'email' => 'email|unique:users,email,'.$this->id.',id',
                'password' => 'string|min:6|max:10',
            ]
        ];
        return $rules[$usage];
    }
}
