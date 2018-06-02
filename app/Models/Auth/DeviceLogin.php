<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Model;

class DeviceLogin extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    |
    | Here is where you can specify relationships for this model.
    |
    */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
